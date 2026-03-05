const express = require("express");
const router = express.Router();

//Authguard For User
const authguard = require("../../middleware/user/auth_user.js");

// Api Controllers
const account = require("../../controllers/user/account.js");
const game = require("../../controllers/user/game.js");

// Account Api
router.route("/account/login").post(account.login);
router.route("/account/register").post(account.register);
router.route("/account/profile-details").get(authguard, account.profileDetails);
router.route("/account/logout").get(authguard, account.logout);
router
  .route("/account/validate-session")
  .get(authguard, account.validateSession);

router.route("/account/check-linking-token").post(account.checkLinkingToken);

router.route("/game/game-create").post(authguard, game.gameCreate);
router.route("/game/game-list").post(authguard, game.gameList);
router.route("/game/findGameOrCreate").post(authguard, game.findGameOrCreate);
router.route("/game/game-chats").post(authguard, game.gameChats);
router.route("/game/game-emojis").post(authguard, game.gameEmojis);

router.route("/game/game-user-create").post(authguard, game.gameUserCreate);
router.route("/game/game-buy-coin").post(authguard, game.gameBuyCoin);
router.route("/game/game-user-quit").post(authguard, game.gameUserQuit);
router.route("/game/game-peer-update").post(authguard, game.gameUserPeerUpdate);
router
  .route("/game/game-sitting-update")
  .post(authguard, game.gameUserSittingUpdate);

router.route("/account/testing").post(account.testing);

// Linking Api
router.route("/public-user-login").post(account.publicUserLogin);
router.route("/public-game-list").post(game.publicGameList);

// Export Router
module.exports = router;
