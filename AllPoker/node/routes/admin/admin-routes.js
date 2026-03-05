const express = require("express");
const router = express.Router();

// Api Controllers
const user = require("../../controllers/admin/user.js");
const game = require("../../controllers/admin/game.js");


console.log("admin router", router)

// Linking Api
router.route("/public-user-login").post(user.publicUserLogin);
router.route("/public-game-list").get(game.publicGameList);

// Export Router
module.exports = router;
