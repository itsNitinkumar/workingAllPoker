const io = require('socket.io-client');
const mediasoupClient = require('mediasoup-client');

const socket = io('/mediasoup');

const urlParams = new URLSearchParams(window.location.search);
const roomName = urlParams.get('id');

// console.log("urlParams", urlParams);
// console.log("roomName", roomName);

// Local Video
let localVideo = document.getElementById('localVideo');
let remoteVideo = document.getElementById('remoteVideo');

let device;
let rtpCapabilities;
let producerTransport;
let consumerTransport;
let producer;
let consumer;

let isProducer = false;

let params = {
    encoding: [
        {
            rid: "r0",
            maxBitrate: 100000,
            scalabilityMode: "S1T3",
        },
        {
            rid: "r1",
            maxBitrate: 300000,
            scalabilityMode: "S1T3",
        },
        {
            rid: "r2",
            maxBitrate: 900000,
            scalabilityMode: "S1T3",
        }
    ],
    codecOptions: {
        videoGoogleStartBitrate: 1000
    }
}

const streamSuccess = async (stream) => {
    let streamActive = stream.active;
    localVideo.srcObject = stream;
    // localVideo.muted = true;
    // localVideo.play();

    console.log("stream", stream);
    // console.log("localVideo", localVideo);

    isProducer = true;

    const track = stream.getVideoTracks()[0];
    params = {
        track,
        ...params
    }

    getRtpCapabilities();

    // setInterval(() => {
    //     if (streamActive != stream.active) {
    //         location.reload();
    //         console.log("stream", stream)
    //     }

    //     console.log("streamActive", stream.active)
    // }, 10000)
}

const getlocalStream = () => {
    navigator.getUserMedia = (navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia);

    navigator.getUserMedia({
        audio: false,
        video: true
        // video: {
        //     width: {
        //         min: 640,
        //         max: 1920
        //     },
        //     height: {
        //         min: 480,
        //         max: 1080
        //     }
        // }
    }, streamSuccess, error => {
        console.log(error);
    });
}

const getRtpCapabilities = async () => {
    socket.emit('getRtpCapabilities', (data) => {
        console.log("rtpCapabilities", data.rtpCapabilities);
        rtpCapabilities = data.rtpCapabilities

        createDevice();
    })
}

const createDevice = async () => {
    try {
        device = new mediasoupClient.Device()

        await device.load({
            routerRtpCapabilities: rtpCapabilities
        })

        console.log("RTP Capabilities", rtpCapabilities);

        if (isProducer) {
            createSendTransport();
        } else {
            checkProducerExist();
        }
    } catch (error) {
        console.log(error);
    }
}

const createSendTransport = async () => {
    console.log("createSendTransport");
    socket.emit("createWebRtcTransport", { sender: true }, ({ params }) => {
        if (params.error) {
            console.log(params.error);
            return;
        }

        // console.log("params", params);

        producerTransport = device.createSendTransport(params);

        producerTransport.on("connect", ({ dtlsParameters }, callback, errorback) => {
            try {
                // Signal local DTLS parameters to the server side transport
                socket.emit("transport-connect", {
                    // trandportId: producerTransport.id,
                    dtlsParameters: dtlsParameters
                })

                // Tell the transport that parameters were transmitted
                callback()
            } catch (error) {
                console.log(error);
                errorback(error);
            }
        })

        producerTransport.on("produce", async (parameters, callback, errorback) => {
            // console.log(parameters)

            try {
                socket.emit("transport-produce", {
                    // transportId: producerTransport.id,
                    kind: parameters.kind,
                    rtpParameters: parameters.rtpParameters,
                    appData: parameters.appData
                }, ({ id }) => {
                    callback({ id })
                })

            } catch (error) {
                console.log(error);
                errorback(error);
            }
        })

        connectSendTransport();
    })

}

const connectSendTransport = async () => {
    producer = await producerTransport.produce(params);

    producer.on("trackended", () => {
        console.log("trackended");
    })
}


const createReceiveTransport = async () => {
    await socket.emit("createWebRtcTransport", { sender: false }, async ({ params }) => {
        if (params.error) {
            console.log(params.error);
            return;
        }

        // console.log("params", params);

        // Create a consumer transport
        consumerTransport = await device.createRecvTransport(params);
        consumerTransport.on("connect", ({ dtlsParameters }, callback, errorback) => {
            try {
                // Signal local DTLS parameters to the server side transport
                socket.emit("transport-recv-connect", {
                    // transportId: consumerTransport.id,
                    dtlsParameters: dtlsParameters
                })

                // Tell the transport that parameters were transmitted
                callback()
            } catch (error) {
                console.log(error);
                errorback(error);
            }
        })

        connectReceiveTransport();
    })
}

const checkProducerExist = async () => {
    socket.emit('checkProducerExist', (producerId) => {
        console.log("checkProducerExist", producerId);
        if (producerId) {
            createReceiveTransport();
        }
    })
}

const connectReceiveTransport = async () => {

    console.log("connectReceiveTransport")

    await socket.emit("consume", {
        rtpCapabilities: device.rtpCapabilities,
    }, async ({ params }) => {

        console.log("connectReceiveTransport", params);

        if (params.error) {
            console.log("connectReceiveTransport", params);
            return;
        }

        // console.log("params", params);
        // console.log("params.id", params.id);
        // console.log("params.producerId", params.producerId);
        // console.log("params.kind", params.kind);

        consumer = await consumerTransport.consume({
            id: params.id,
            producerId: params.producerId,
            kind: params.kind,
            rtpParameters: params.rtpParameters
        });

        const { track } = consumer;

        remoteVideo.srcObject = new MediaStream([track]);

        socket.emit("consumer-resume")
    })
}



// Socket
socket.on('connection-success', () => {
    socket.emit("joinRoom", {
        roomName
    });
})

socket.on('streamPublished', () => {
    console.log("streamPublished");

    if (!isProducer) {
        setTimeout(() => {
            location.reload();
        }, 10000)
    }
})

socket.on('streamUnpublished', () => {
    console.log("streamUnpublished");

    if (!isProducer) {
        setTimeout(() => {
            location.reload();
        }, 5000)
    }
})

socket.on('reloadPage', () => {
    console.log("reloadPage");

    location.reload();
})



// All Buttons
$("#publishVideoBtn").click(() => {
    $("#publishVideoBtn").hide();
    getlocalStream();
});

$("#consumeVideoBtn").click(() => {
    if (device) {
        checkProducerExist();
    } else {
        getRtpCapabilities();
    }
})