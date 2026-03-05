import express from "express";
const app = express();

// MediaSoup
import mediasoup from "mediasoup";

import https from "httpolyglot";
import fs from "fs";

// Path
import path from "path";
const __dirname = path.resolve();

// Port
const port = "5000";

app.get("/", (req, res) => {
    res.send("MediaSoup Server");
})

app.use("/sfu", express.static(path.join(__dirname, "public")));

var privateKey = fs.readFileSync('/etc/ssl/private/ssl.key', 'utf8');
var certificate = fs.readFileSync('/etc/ssl/certs/ssl.crt', 'utf8');
var credentials = {
    key: privateKey,
    cert: certificate
};

const httpsServer = https.createServer(credentials, app);
httpsServer.listen(port, function () {
    console.log(`listening on https ${port}`);
});

// Socket.io
import { Server } from "socket.io";
const io = new Server(httpsServer);
const peers = io.of("/mediasoup");

// MediaSoup
let worker;
let router;
let producerTransport;
let consumerTransport;
let producer;
let consumer;


const createworker = async () => {
    worker = await mediasoup.createWorker({
        rtcMinPort: 2000,
        rtcMaxPort: 2020
    })

    console.log(`Worker pid => ${worker.pid}`);

    worker.on("died", () => {
        console.log("Worker died");
        setTimeout(() => process.exit(1), 2000);
    })

    return worker;
}

worker = createworker();

const mediaCodecs = [
    {
        kind: "audio",
        mimeType: "audio/opus",
        clockRate: 48000,
        channels: 2
    }, {
        kind: "video",
        mimeType: "video/VP8",
        clockRate: 90000,
        parameters: {
            "x-google-start-bitrate": 100
        }
    }
]

peers.on("connection", async (socket) => {
    console.log("Peer connected", socket.id);
    socket.emit("connection-success", {
        socketId: socket.id,
        existProducer: producer ? true : false
    })

    socket.on("disconnect", () => {
        console.log("Peer disconnected", socket.id);
    })

    socket.on("createRoom", async (callback) => {
        if (router === undefined) {
            router = await worker.createRouter({ mediaCodecs })
            console.log(`Router Id => ${router.id}`);
        }

        getRtpCapabilities(callback)
    })

    socket.on("createWebRtcTransport", async ({ sender }, callback) => {
        console.log(`Is sender: ${sender}`);

        if (sender) {
            producerTransport = await createWebRtcTransport(callback)
        } else {
            consumerTransport = await createWebRtcTransport(callback)
        }
    })

    socket.on("transport-connect", async ({ dtlsParameters }) => {
        console.log("transport-connect", dtlsParameters);

        await producerTransport.connect({ dtlsParameters })
    })

    socket.on("transport-produce", async ({ kind, rtpParameters, appData }, callback) => {
        console.log("transport-produce", { kind, rtpParameters, appData });

        producer = await producerTransport.produce({
            kind,
            rtpParameters
        })

        producer.on("transportclose", () => {
            console.log("producer transport closed");
            producer.close()
        })

        callback({
            id: producer.id
        })

    })

    socket.on("transport-recv-connect", async ({ dtlsParameters }) => {
        console.log("transport-recv-connect", dtlsParameters);
        await consumerTransport.connect({ dtlsParameters })
    })

    socket.on("consume", async ({ rtpCapabilities }, callback) => {
        try {
            if (router.canConsume({ producerId: producer.id, rtpCapabilities })) {
                consumer = await consumerTransport.consume({
                    producerId: producer.id,
                    rtpCapabilities,
                    paused: true
                })

                consumer.on("transportclose", () => {
                    console.log("Transport closed by consumer");
                })

                consumer.on("producerclose", () => {
                    console.log("Transport closed by Producer");
                })

                const params = {
                    id: consumer.id,
                    producerId: producer.id,
                    kind: consumer.kind,
                    rtpParameters: consumer.rtpParameters
                }

                callback({ params })
            }
        } catch (error) {
            callback({
                params: { error: error }
            })
        }
    })

    socket.on("consumer-resume", async () => {
        console.log("consumer-resume");
        await consumer.resume()
    })
})

const createWebRtcTransport = async (callback) => {
    try {
        const webRtcTransport_options = {
            listenIps: [{ ip: "80.209.238.145" }],
            enableUdp: true,
            enableTcp: true,
            preferUdp: true
        }

        let transport = await router.createWebRtcTransport(webRtcTransport_options);
        console.log("transport", transport.id);

        transport.on("dtlsstatechange", dtlsstate => {
            console.log("dtlsstatechange", dtlsstate);

            if (dtlsstate === "closed") {
                transport.close();
            }
        })

        transport.on("close", () => {
            console.log("transport closed");
        })

        callback({
            params: {
                id: transport.id,
                iceParameters: transport.iceParameters,
                iceCandidates: transport.iceCandidates,
                dtlsParameters: transport.dtlsParameters
            }
        })

        return transport;
    } catch (error) {
        console.log(error);
        callback({
            params: { error }
        })
    }
}

const getRtpCapabilities = async (callback) => {
    const rtpCapabilities = router.rtpCapabilities;

    callback({ rtpCapabilities })
}