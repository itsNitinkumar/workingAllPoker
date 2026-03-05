const express = require("express");
const cors = require("cors");

const dotenv = require("dotenv");
dotenv.config();

// const path = require('path');
const path = require("node:path");
var fs = require("fs");
var ExpressPeerServer = require("peer").ExpressPeerServer;

const app = express();
const router = express.Router();
const http = require("http");
const https = require("https");
const port = 3200; // local HTTP port
const httpsPort = 3000;

// Environment
const isProduction = process.env.NODE_ENV === "production";
const HTTP_PORT = process.env.HTTP_PORT || 3200;
const HTTPS_PORT = process.env.HTTPS_PORT || 3000;
// Cors
app.use(cors());

// Import Models
const db = require("./models");
db.sequelize.sync();

// Import Routes
const adminRoutes = require("./routes/admin/admin-routes");
const userRoutes = require("./routes/user/user-routes");
const dealerRoutes = require("./routes/dealer/dealer-routes");

// Parse application/json
app.use((req, res, next) => {
  express.json({ limit: "200mb" })(req, res, next);
});

// parse application/x-www-form-urlencoded
app.use(
  express.urlencoded({ limit: "200mb", extended: true, parameterLimit: 50000 })
);

// Serve static files
app.use(express.static("public"));
app.use(express.static("views"));
app.use("/api", express.static(path.join(__dirname, "public")));

// Routes
// ===============================
app.use("/api/admin", adminRoutes);
app.use("/api/user", userRoutes);
app.use("/api/dealer", dealerRoutes);

// Run Angular
// ===============================
if (isProduction) {
  app.use("/", express.static(path.join("/var/www/html")));
  app.use("/game*", express.static(path.join("/var/www/html")));
  app.use("/admin*", express.static(path.join("/var/www/html")));
  app.use("/dealer*", express.static(path.join("/var/www/html")));
} else {
  // Local environment fallback (for development)
  app.use("/", express.static(path.join(__dirname, "views")));
}

// Run HTTPS Server
// ===============================
// var certificate = fs.readFileSync('/etc/ssl/certs/ssl-cert-snakeoil.pem', 'utf8');
// var privateKey = fs.readFileSync('/etc/ssl/private/ssl-cert-snakeoil.key', 'utf8');
// var certificate = fs.readFileSync('/etc/letsencrypt/live/game.allcardroom.com/fullchain.pem', 'utf8');
// var privateKey = fs.readFileSync('/etc/letsencrypt/live/game.allcardroom.com/privkey.pem', 'utf8');
// var credentials = {
//   key: privateKey,
//   cert: certificate,
// };
// ------------------------------
// Global Error Handler
// ------------------------------
app.use((err, req, res, next) => {
  console.error("🔥 Error Middleware:", err.stack);

  res.status(err.status || 500).json({
    success: false,
    message: err.message || "Internal Server Error",
  });
});

// HTTP Server (always runs)
const httpServer = http.createServer(app);

// Attach Socket.IO to HTTP server (for local + fallback)
const { Server } = require("socket.io");
const socketChat = require("./controllers/common/socket.js");

const io = new Server(httpServer, {
  cors: { origin: "*", methods: ["GET", "POST"] },
  transports: ["websocket", "polling"],
  path: "/api/socket.io",
});
socketChat(io);

// PeerJS Local
const peerServer = ExpressPeerServer(httpServer, {
  debug: true,
  allow_discovery: true,
});
app.use("/peerjsLocal", peerServer);

// HTTPS setup for production only
if (isProduction) {
  try {
    const privateKey = fs.readFileSync(
      "/etc/letsencrypt/live/game.allcardroom.com/privkey.pem",
      "utf8"
    );
    const certificate = fs.readFileSync(
      "/etc/letsencrypt/live/game.allcardroom.com/fullchain.pem",
      "utf8"
    );
    const credentials = { key: privateKey, cert: certificate };

    const httpsServer = https.createServer(credentials, app);

    // Attach HTTPS Socket.IO
    const ioHttps = new Server(httpsServer, {
      cors: { origin: "*", methods: ["GET", "POST"] },
      transports: ["websocket", "polling"],
      path: "/api/socket.io",
    });
    socketChat(ioHttps);

    httpsServer.listen(HTTPS_PORT, () => {
      console.log(`🔒 HTTPS server running on port ${HTTPS_PORT}`);
    });
  } catch (err) {
    console.error("⚠️ SSL setup failed:", err.message);
  }
}

// Start HTTP server always
httpServer.listen(HTTP_PORT, () => {
  console.log(`✅ HTTP server running on port ${HTTP_PORT}`);
});
