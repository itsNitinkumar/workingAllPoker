const express = require("express");
const mediasoup = require('mediasoup');

const fs = require('fs');

const app = express();
const port = "5000";

const mediaCodecs = [
    {
        kind: "audio",
        mimeType: "audio/opus",
        clockRate: 48000,
        channels: 2
    },
    {
        kind: "video",
        mimeType: "video/VP8",
        clockRate: 90000,
        parameters:
        {
            "packetization-mode": 1,
            "profile-level-id": "42e01f",
            "level-asymmetry-allowed": 1
        }
    }
];


async function multipleBroadcast() {
    console.log("multipleBroadcast")

    // Have two workers.
    const worker1 = await mediasoup.createWorker();
    const worker2 = await mediasoup.createWorker();

    // console.log("worker1", worker1)
    // console.log("worker2", worker2)

    // Create a router in each worker.
    const router1 = await worker1.createRouter({ mediaCodecs });
    const router2 = await worker2.createRouter({ mediaCodecs });

    // console.log("router1", router1)
    // console.log("router2", router2)

    // Produce in router1.
    const transport1 = await router1.createWebRtcTransport({
        // Use webRtcServer or listenIps
        // webRtcServer: webRtcServer,
        listenIps: [{ ip: "80.209.238.145", announcedIp: "80.209.238.145" }],
        enableUdp: true,
        enableTcp: true,
        preferUdp: true
    });

    const producer1 = await transport1.produce({
        kind: "video",
        rtpParameters: {
            mid: "1",
            codecs: [
                {
                    mimeType: "video/VP8",
                    payloadType: 101,
                    clockRate: 90000,
                    rtcpFeedback: [
                        { type: "nack" },
                        { type: "nack", parameter: "pli" },
                        { type: "ccm", parameter: "fir" },
                        { type: "goog-remb" }
                    ]
                },
                {
                    mimeType: "video/rtx",
                    payloadType: 102,
                    clockRate: 90000,
                    parameters: { apt: 101 }
                }
            ],
            headerExtensions: [
                { id: 2, uri: "urn:ietf:params:rtp-hdrext:sdes:mid" },
                { id: 3, uri: "urn:ietf:params:rtp-hdrext:sdes:rtp-stream-id" },
                { id: 5, uri: "urn:3gpp:video-orientation" },
                { id: 6, uri: "http://www.webrtc.org/experiments/rtp-hdrext/abs-send-time" }
            ],
            encodings: [
                { rid: "r0", active: true, maxBitrate: 100000 },
                { rid: "r1", active: true, maxBitrate: 300000 },
                { rid: "r2", active: true, maxBitrate: 900000 }
            ],
            rtcp: { cname: "Zjhd656aqfoo" }
        }
    });

    // Pipe producer1 into router2.
    await router1.pipeToRouter({ producerId: producer1.id, router: router2 });

    // Consume producer1 from router2.
    const transport2 = await router2.createWebRtcTransport({
        // Use webRtcServer or listenIps
        // webRtcServer: webRtcServer,
        listenIps: [{ ip: "80.209.238.145", announcedIp: "80.209.238.145" }],
        enableUdp: true,
        enableTcp: true,
        preferUdp: true
    });

    const consumer2 = await transport2.consume({
        producerId: producer1.id,
        rtpCapabilities: {
            codecs: [
                {
                    mimeType: "audio/opus",
                    kind: "audio",
                    clockRate: 48000,
                    preferredPayloadType: 100,
                    channels: 2
                },
                {
                    mimeType: "video/VP8",
                    kind: "video",
                    clockRate: 90000,
                    preferredPayloadType: 101,
                    rtcpFeedback:
                        [
                            { type: "nack" },
                            { type: "nack", parameter: "pli" },
                            { type: "ccm", parameter: "fir" },
                            { type: "goog-remb" }
                        ],
                    parameters:
                    {
                        "level-asymmetry-allowed": 1,
                        "packetization-mode": 1,
                        "profile-level-id": "4d0032"
                    }
                },
                {
                    mimeType: "video/rtx",
                    kind: "video",
                    clockRate: 90000,
                    preferredPayloadType: 102,
                    rtcpFeedback: [],
                    parameters:
                    {
                        apt: 101
                    }
                }
            ],
            headerExtensions: [
                {
                    kind: "video",
                    uri: "http://www.webrtc.org/experiments/rtp-hdrext/abs-send-time", // eslint-disable-line max-len
                    preferredId: 4,
                    preferredEncrypt: false
                },
                {
                    kind: "audio",
                    uri: "urn:ietf:params:rtp-hdrext:ssrc-audio-level",
                    preferredId: 8,
                    preferredEncrypt: false
                },
                {
                    kind: "video",
                    uri: "urn:3gpp:video-orientation",
                    preferredId: 9,
                    preferredEncrypt: false
                },
                {
                    kind: "video",
                    uri: "urn:ietf:params:rtp-hdrext:toffset",
                    preferredId: 10,
                    preferredEncrypt: false
                }
            ]
        }
    });
}

multipleBroadcast();

// Run HTTPS Server
// ===============================
var privateKey = fs.readFileSync('/etc/ssl/private/ssl.key', 'utf8');
var certificate = fs.readFileSync('/etc/ssl/certs/ssl.crt', 'utf8');
var credentials = {
    key: privateKey,
    cert: certificate
};
var httpsServer = require('https').Server(credentials, app);

httpsServer.listen(port, function () {
    console.log(`listening on https ${port}`);
});
