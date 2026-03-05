const mqtt = require("mqtt");
// mosquitto -c /etc/mosquitto/conf.d/default.conf -v

// const mqttClient = mqtt.connect("mqtt://test.mosquitto.org");
// const mqttClient = mqtt.connect("mqtt://80.209.238.145");
// const mqttClient = mqtt.connect("mqtt://192.168.12.241");


// const mqttClient = mqtt.connect("mqtt://80.209.238.145", {
//     // clientId: "dc84c7d0-f1c9-494f-bbe9-8f0204a2e197",
// });

// console.log("mqttClient", mqttClient);

// Mux1: 1-9 (Players)
// Mux1: 10 (Deck)
// Mux1: 11 (Table)

// mqttClient.on("connect", (data) => {
//     console.log("connected");
//     // console.log(data);
//     mqttClient.subscribe("/to/my/topic", (err) => {
//         if (!err) {
//             mqttClient.publish("/to/my/topic", "Hello mqtt from localhost");
//             console.log("subscribed");
//         } else {
//             console.log("err", err);
//         }
//     });
// });

// mqttClient.on("message", (topic, message) => {
//     // message is Buffer

//     console.log("message", topic);
//     console.log(message.toString());


//     // KA12345#
//     // KA12345£
//     // mqttClient.end();
// });