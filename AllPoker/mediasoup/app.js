import express from "express";
const app = express();

// MediaSoup
import mediasoup from "mediasoup";

import fs from "fs";
import https from "httpolyglot";

// Path
import path from "path";
const __dirname = path.resolve();

// Port
const port = "5000";

app.get("/", (req, res) => {
  res.send("MediaSoup Server");
});

app.use("/sfu", express.static(path.join(__dirname, "public")));
const isProduction = process.env.NODE_ENV === "production";

let httpsServer;

if (isProduction) {
  var certificate = fs.readFileSync(
    "/etc/letsencrypt/live/game.allcardroom.com/fullchain.pem",
    "utf8"
  );
  var privateKey = fs.readFileSync(
    "/etc/letsencrypt/live/game.allcardroom.com/privkey.pem",
    "utf8"
  );
  var credentials = {
    key: privateKey,
    cert: certificate,
  };
  httpsServer = https.createServer(credentials, app);
} else {
  // Local dev: use plain HTTP (import http module)
  const { createServer } = await import("http");
  httpsServer = createServer(app);
}

httpsServer.listen(port, function () {
  console.log(`listening on ${isProduction ? "https" : "http"} ${port}`);
});

// Socket.io
import { Server } from "socket.io";
const io = new Server(httpsServer);
const socketConnection = io.of("/mediasoup");

// MediaSoup
let worker;
let router;
let producerTransport;
let consumerTransport;
let producer;
let consumer;

// let
let allRooms = {};
let allPeers = {};

let allTransports = [];
let allProducers = [];
let allConsumers = [];

const createworker = async () => {
  worker = await mediasoup.createWorker({
    // rtcMinPort: 2000,
    // rtcMaxPort: 2020
    rtcMinPort: 40000,
    rtcMaxPort: 49999,
  });

  console.log(`Worker pid => ${worker.pid}`);

  worker.on("died", () => {
    // console.log("Worker died");
    setTimeout(() => process.exit(1), 2000);
  });

  return worker;
};

worker = createworker();

const mediaCodecs = [
  {
    kind: "audio",
    mimeType: "audio/opus",
    clockRate: 48000,
    channels: 2,
  },
  {
    kind: "video",
    mimeType: "video/VP8",
    clockRate: 90000,
    parameters: {
      "x-google-start-bitrate": 100,
    },
  },
];

// Reload Page
let restart = false;
if (!restart) {
  setTimeout(() => {
    if (socketConnection) {
      console.log("restart", restart);
      socketConnection.emit("reloadPage", true);
    }
  }, 10000);
  restart = true;
}

try {
  socketConnection.on("connection", async (socket) => {
    console.log("Peer connected", socket.id);
    socket.emit("connection-success", {
      socketId: socket.id,
    });

    socket.on("joinRoom", async (req) => {
      // console.log("joinRoom", req);
      socket.join(req.roomName);

      allPeers[socket.id] = {
        roomName: req.roomName,
      };

      if (!allRooms[req.roomName]) {
        let router1 = await worker.createRouter({ mediaCodecs });

        allRooms[req.roomName] = {
          router: router1,
          producer: {},
        };
      }
    });

    socket.on("disconnect", () => {
      console.log("Peer disconnected", socket.id);
      // removeItems()

      removeItems("transports", socket.id, "roomName");
      removeItems("producers", socket.id, "roomName");
      removeItems("consumers", socket.id, "roomName");

      if (allPeers[socket.id]) {
        delete allPeers[socket.id];
      }

      if (allPeers[socket.id]) {
        let roomName = allPeers[socket.id].roomName;
        producer = allRooms[roomName].producer;

        if (!producer.id) {
          socket.to(roomName).emit("streamUnpublished", {
            socketId: socket.id,
          });
        }
      }
    });

    // if (!router) {
    //     router = await worker.createRouter({ mediaCodecs })
    // }

    socket.on("getRtpCapabilities", async (callback) => {
      let router;
      if (allPeers[socket.id]) {
        let roomName = allPeers[socket.id].roomName;
        router = allRooms[roomName].router;
      }

      if (router) {
        const rtpCapabilities = router.rtpCapabilities;

        callback({ rtpCapabilities });
      }
    });

    socket.on("createWebRtcTransport", async ({ sender }, callback) => {
      console.log(`Is sender: ${sender}`);
      let router;
      if (allPeers[socket.id]) {
        let roomName = allPeers[socket.id].roomName;
        router = allRooms[roomName].router;
      }

      createWebRtcTransport(router).then(
        (transport) => {
          callback({
            params: {
              id: transport.id,
              iceParameters: transport.iceParameters,
              iceCandidates: transport.iceCandidates,
              dtlsParameters: transport.dtlsParameters,
            },
          });

          if (sender) {
            producerTransport = transport;
          }

          addItems("transports", transport, socket.id, "roomName");
        },
        (error) => {
          console.log("createWebRtcTransport error", error);
        }
      );
    });

    socket.on("transport-connect", async ({ dtlsParameters }) => {
      // console.log("transport-connect", dtlsParameters);
      console.log("transport-connect", socket.id);

      try {
        let producerTransport = await getItems(
          "transports",
          socket.id,
          "roomName"
        ).transport;
        await producerTransport.connect({ dtlsParameters });
      } catch (error) {
        console.log("transport-connect", error);
      }
    });

    socket.on(
      "transport-produce",
      async ({ kind, rtpParameters, appData }, callback) => {
        console.log("transport-produce", { kind, rtpParameters, appData });

        let producerTransport = await getItems(
          "transports",
          socket.id,
          "roomName"
        ).transport;

        let producer = await producerTransport.produce({
          kind,
          rtpParameters,
        });

        producer.on("transportclose", () => {
          console.log("producer transport closed");
          producer.close();
        });

        if (allPeers[socket.id]) {
          let roomName = allPeers[socket.id].roomName;
          allRooms[roomName].producer = producer;

          socket.to(roomName).emit("streamPublished", {
            socketId: socket.id,
          });
        }

        callback({
          id: producer.id,
        });
      }
    );

    socket.on("transport-recv-connect", async ({ dtlsParameters }) => {
      console.log("transport-recv-connect", dtlsParameters);

      // console.log("getItems(transports)", getItems("transports", socket.id, "roomName").transport);
      // console.log("consumerTransport", consumerTransport);

      try {
        let consumerTransport = await getItems(
          "transports",
          socket.id,
          "roomName"
        ).transport;
        await consumerTransport.connect({ dtlsParameters });
      } catch (error) {
        console.log("transport-recv-connect", error);
      }
    });

    socket.on("consume", async ({ rtpCapabilities }, callback) => {
      let roomName;
      let router;
      let producer;

      if (allPeers[socket.id]) {
        roomName = allPeers[socket.id].roomName;

        router = allRooms[roomName].router;
        producer = allRooms[roomName].producer;
      }

      // console.log("producer.id", producer.id);
      // console.log("rtpCapabilities", rtpCapabilities);

      try {
        if (router.canConsume({ producerId: producer.id, rtpCapabilities })) {
          let consumerTransport = await getItems(
            "transports",
            socket.id,
            roomName
          ).transport;

          consumer = await consumerTransport.consume({
            producerId: producer.id,
            rtpCapabilities,
            paused: true,
          });

          consumer.on("transportclose", () => {
            console.log("Transport closed by consumer");
          });

          consumer.on("producerclose", () => {
            console.log("Transport closed by Producer Room Name", roomName);

            consumerTransport.close([]);
            consumer.close();

            allRooms[roomName].producer = {};

            socket.to(roomName).emit("streamUnpublished", {
              socketId: socket.id,
            });
          });

          addItems("consumers", consumer, socket.id, roomName);

          const params = {
            id: consumer.id,
            producerId: producer.id,
            kind: consumer.kind,
            rtpParameters: consumer.rtpParameters,
          };

          callback({ params });
        }
      } catch (error) {
        console.log(
          "router.canConsume({ producerId: producer.id, rtpCapabilities })",
          error
        );

        callback({
          params: { error: error },
        });
      }
    });

    socket.on("consumer-resume", async () => {
      // console.log("consumer-resume");

      // console.log("getItems(consumers)", getItems("consumers", socket.id, "roomName").consumer);
      // console.log("consumer", consumer);

      // let consumer = getItems("consumers", socket.id, "roomName").consumer
      await consumer.resume();
    });

    socket.on("checkProducerExist", async (callback) => {
      let producer;
      if (allPeers[socket.id]) {
        let roomName = allPeers[socket.id].roomName;
        producer = allRooms[roomName].producer;
      }

      callback(producer.id);
    });
  });

  const createWebRtcTransport = async (router) => {
    return new Promise(async (resolve, reject) => {
      try {
        const webRtcTransport_options = {
          listenIps: [{ ip: "0.0.0.0", announcedIp: "127.0.0.1" }],
          enableUdp: true,
          enableTcp: true,
          preferUdp: true,
        };

        let transport = await router.createWebRtcTransport(
          webRtcTransport_options
        );
        // console.log("transport", transport.id);

        transport.on("dtlsstatechange", (dtlsstate) => {
          // console.log("dtlsstatechange", dtlsstate);

          if (dtlsstate === "closed") {
            transport.close();
          }
        });

        transport.on("close", () => {
          console.log("transport closed");
        });

        resolve(transport);
      } catch (error) {
        reject(error);
      }
    });
  };

  //
  const addItems = (type, item, socketId, roomName) => {
    if (type == "transports") {
      allTransports.push({
        socketId,
        transport: item,
        roomName,
      });
    } else if (type == "producers") {
      allProducers.push({
        socketId,
        producer: item,
        roomName,
      });
    } else if (type == "consumers") {
      allConsumers.push({
        socketId,
        consumer: item,
        roomName,
      });
    }
  };

  const getItems = (type, socketId, roomName) => {
    if (type == "transports") {
      return allTransports.find((item) => item.socketId == socketId);
    } else if (type == "producers") {
      return allProducers.find((item) => item.socketId == socketId);
    } else if (type == "consumers") {
      return allConsumers.find((item) => item.socketId == socketId);
    }
  };

  const removeItems = (type, socketId, roomName) => {
    if (type == "transports") {
      allTransports = allTransports.filter((item) => item.socketId != socketId);
    } else if (type == "producers") {
      allProducers = allProducers.filter((item) => item.socketId != socketId);
    } else if (type == "consumers") {
      allConsumers = allConsumers.filter((item) => item.socketId != socketId);
    }
  };
} catch (error) {
  console.log("Mediasoup ERROR => ", error);
}
