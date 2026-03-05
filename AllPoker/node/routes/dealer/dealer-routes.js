const express = require("express");
const router = express.Router();

//Authguard For User
const authguard = require("../../middleware/dealer/auth_dealer.js");

// Api Controllers
const account = require("../../controllers/dealer/account.js");
const game = require("../../controllers/dealer/game.js");

// Account Api
router.route("/account/login").post(account.login);
router.route("/account/profile-details").get(authguard, account.profileDetails);

router.route("/game/game-list").post(authguard, game.gameList);

// Export Router
module.exports = router;
