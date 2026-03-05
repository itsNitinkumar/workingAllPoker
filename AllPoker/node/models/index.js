const Sequelize = require("sequelize");

const host = process.env.DB_HOST || "127.0.0.1";
const user = process.env.DB_USER;
const pass = process.env.DB_PASSWORD;

const sequelize = new Sequelize("pure_view", user, pass, {
  host: host,
  dialect: "mysql",
  operatorsAliases: 0,
  port: "3306",

  pool: {
    max: 15,
    min: 5,
    idle: 20000,
    evict: 15000,
    acquire: 30000,
  },
  define: {
    paranoid: true,
    // alter: true
  },

  // Disable Console Log
  logging: false,
});
//
const db = {};

db.Sequelize = Sequelize;
db.sequelize = sequelize;

//
db.userModel = require("./users.model.js")(db);
db.gameUserModel = require("./game_users.model.js")(db);
db.gameDetailsModel = require("./game_details.model.js")(db);
db.gameModel = require("./games.model.js")(db);

db.gameEmojisModel = require("./game_emojis.model.js")(db);
db.gameChatsModel = require("./game_chats.model.js")(db);

// Module Export
module.exports = db;
