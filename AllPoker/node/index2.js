const express = require("express");
const cors = require("cors");

const app = express();
const router = express.Router();
const port = "3200";

//
app.use(cors());

// Import Models
const db = require("./models");
db.sequelize.sync();

// Import Routes
const userRoutes = require("./routes/user/user-routes");

// parse application/json
app.use((req, res, next) => {
  express.json({ limit: "200mb" })(req, res, next);
});

// parse application/x-www-form-urlencoded
app.use(
  express.urlencoded({ limit: "200mb", extended: true, parameterLimit: 50000 })
);

//
app.use("/api/user", userRoutes);

// Run Server
const server = require("http").createServer(app);

// Import MQTT
require("./controllers/common/mqtt.js");

// Import Socket
const socketChat = require('./controllers/common/socket.js');
const io = require('socket.io')(server, {
  cors: {
    origin: '*',
    methods: ['GET', 'POST']
  },
  transport: ['websocket'],
  multiplex: false,
  path: '/api/socket.io'
})

socketChat(io)

server.listen(port, () => {
  console.log(`Server running on port ${port}`);
});
