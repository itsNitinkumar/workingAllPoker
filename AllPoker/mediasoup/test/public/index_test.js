const io = require('socket.io-client');
const mediasoupClient = require('mediasoup-client');

const roomName = window.location.pathname.split('/')[2];

const socket = io('/mediasoup');

socket.on('connection-success', (socket, existProducer) => {
    console.log(socket.socketId, existProducer);

    getlocalStream()
})

// Local Video
let localVideo = document.getElementById('localVideo');
let remoteVideo = document.getElementById('remoteVideo');
let videoContainer = document.getElementById('videoContainer');

let device;
let rtpCapabilities;
let producerTransport;
let consumerTransport = [];
let consumerTransports = [];
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

const streamSuccess = (stream) => {
    localVideo.srcObject = stream;

    console.log("stream", stream);
    console.log("localVideo", localVideo);

    const track = stream.getVideoTracks()[0];
    params = {
        track,
        ...params
    }

    // goConnect(true)

    joinRoom()
}

const joinRoom = () => {
    socket.emit('joinRoom', { roomName }, (data) => {
        console.log(`RTP capabilities: ${data.rtpCapabilities}`);

        rtpCapabilities = data.rtpCapabilities

        createDevice();
    })
}

const getlocalStream = () => {
    navigator.mediaDevices.getUserMedia({
        audio: false,
        video: {
            width: {
                min: 640,
                max: 1920
            },
            height: {
                min: 480,
                max: 1080
            }
        }
    }).then(streamSuccess).catch(error => {
        console.log(error);
    })
}

const goConsume = () => {
    goConnect(false)
}

const goConnect = (producerOrConsumer) => {
    isProducer = producerOrConsumer
    device === undefined ? getRtpCapabilities() : goCreateTransport();
}

const goCreateTransport = () => {
    isProducer ? createSendTransport() : createReceiveTransport();
}

const createDevice = async () => {
    try {
        device = new mediasoupClient.Device()

        await device.load({
            routerRtpCapabilities: rtpCapabilities
        })

        console.log("RTP Capabilities", rtpCapabilities);

        // goCreateTransport();

        createSendTransport();
    } catch (error) {
        console.log(error);
    }
}

const getRtpCapabilities = async () => {
    socket.emit('createRoom', (data) => {
        console.log("rtpCapabilities", data.rtpCapabilities);
        rtpCapabilities = data.rtpCapabilities

        createDevice();
    })
}

socket.on("new-producer", ({ producerId }) => signalNewConsumerTransport(producerId))

const getProducers = () => {
    console.log("getProducers");
    socket.emit("getProducers", producerIds => {
        console.log("getProducers", producerIds);

        producerIds.forEach(signalNewConsumerTransport);
    });
}

const createSendTransport = async () => {
    console.log("createSendTransport");
    socket.emit("createWebRtcTransport", { consumer: false }, ({ params }) => {
        if (params.error) {
            console.log(params.error);
            return;
        }

        console.log("params", params);

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
            console.log(parameters)

            try {
                socket.emit("transport-produce", {
                    // transportId: producerTransport.id,
                    kind: parameters.kind,
                    rtpParameters: parameters.rtpParameters,
                    appData: parameters.appData
                }, ({ id, producersExist }) => {
                    callback({ id })

                    if (producersExist) {
                        getProducers();
                    }
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


const signalNewConsumerTransport = async (remoteProducerId) => {
    await socket.emit("createWebRtcTransport", { consumer: true }, ({ params }) => {
        if (params.error) {
            console.log(params.error);
            return;
        }

        console.log("params", params);

        // Create a consumer transport
        consumerTransport = device.createRecvTransport(params);
        consumerTransport.on("connect", ({ dtlsParameters }, callback, errorback) => {
            try {
                // Signal local DTLS parameters to the server side transport
                socket.emit("transport-recv-connect", {
                    dtlsParameters: dtlsParameters,
                    serverConsumerTransportId: params.id
                })

                // Tell the transport that parameters were transmitted
                callback()
            } catch (error) {
                console.log(error);
                errorback(error);
            }
        })

        // Receive a stream
        connectReceiveTransport(consumerTransport, remoteProducerId, params.id);
    })
}

const connectReceiveTransport = async (consumerTransport, remoteProducerId, serverConsumerTransportId) => {
    await socket.emit("consume", {
        rtpCapabilities: device.rtpCapabilities,
        remoteProducerId,
        serverConsumerTransportId
    }, async ({ params }) => {

        console.log("connectReceiveTransport", params);

        if (params.error) {
            console.log("connectReceiveTransport", params.error);
            return;
        }

        console.log("params", params);
        console.log("params.id", params.id);
        console.log("params.producerId", params.producerId);
        console.log("params.kind", params.kind);

        const consumer = await consumerTransport.consume({
            id: params.id,
            producerId: params.producerId,
            kind: params.kind,
            rtpParameters: params.rtpParameters
        });

        consumerTransports = [
            ...consumerTransports,
            {
                consumerTransport,
                serverConsumerTransportId: params.id,
                producerId: remoteProducerId,
                consumer
            }
        ]

        const newElement = document.createElement("div");
        newElement.setAttribute("id", `consumer-${remoteProducerId}`);
        newElement.setAttribute("class", "col-md-6");
        newElement.innerHTML = `<video id="${remoteProducerId}" autoplay muted class="video"></video>`;
        videoContainer.appendChild(newElement);


        const { track } = consumer;

        // remoteVideo.srcObject = new MediaStream([track]);
        document.getElementById(remoteProducerId).srcObject = new MediaStream([track]);

        // socket.emit("consumer-resume")
        socket.emit("consumer-resume", { serverConsumerId: params.serverConsumerId })
    })
}


// 
socket.on('producer-closed', ({ remoteProducerId }) => {
    const producerToClose = consumerTransports.find(producer => producer.producerId === remoteProducerId);
    producerToClose.consumerTransport.close();
    producerToClose.consumer.close();

    // Remove the closed producer
    consumerTransports = consumerTransports.filter(transport => transport.producerId !== remoteProducerId);

    // Remove the video element
    document.getElementById(`consumer-${remoteProducerId}`).remove();
})



// All Buttons
// $("#getVideoBtn").click(() => {
//     console.log("clicked");
//     getlocalStream();
// });
// $("#createReceiveTransportBtn").click(() => {
//     goConsume()
// })