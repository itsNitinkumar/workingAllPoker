const bcrypt = require("bcrypt");

const common = require("../../middleware/common");
const db = require("../../models").default;
const {
  gameModel,
  gameUserModel,
  gameChatsModel,
  gameEmojisModel,
} = require("../../models");
const { CASH_OPERATION } = require("../../utils/constants");
const { callApi, sendResponse } = require("../../services/apiService");

module.exports = {
  gameCreate: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    try {
      const val = {
        token: "123123123",
        status: 1,
      };

      gameModel.create(val).then((result) => {
        resData.status = true;
        resData.message = `New Game Created`;
        res.status(200).json(resData);
      });
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameList: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token } = req.body;

    try {
      let gameList = await gameModel.findAll({
        include: [
          {
            model: gameUserModel,
            as: "game_users",
            where: { status: 1 },
            required: false,
          },
        ],
        where: { token: token, status: 1 },
        sort: [["id", "DESC"]],
      });

      resData.status = true;
      resData.data = gameList;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameChats: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { game_id } = req.body;

    try {
      let gameChats = await gameChatsModel.findAll({
        include: [
          { model: gameEmojisModel, as: "game_emoji", required: false },
        ],
        where: { game_id: game_id },
        order: [["id", "DESC"]],
        limit: 20,
      });

      gameChats = gameChats.sort((a, b) => (a.id > b.id ? 1 : -1));

      resData.status = true;
      resData.data = gameChats;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameEmojis: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    try {
      let gameChats = await gameEmojisModel.findAll({
        where: { status: 1 },
      });

      resData.status = true;
      resData.data = gameChats;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  findGameOrCreate: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token } = req.body;

    console.log("req.user", req.user);

    try {
      let gameDetails = await gameModel.findOne({
        include: [
          {
            model: gameUserModel,
            as: "game_users",
            where: { status: 1 },
            required: false,
          },
        ],
        where: {
          token: token,
          status: 1,
        },
        sort: [["id", "DESC"]],
      });

      if (!gameDetails) {
        let val = {
          token: token,
          table_game: 0,
          status: 1,
        };

        await gameModel.create(val);

        gameDetails = await gameModel.findOne({
          include: [
            { model: gameUserModel, as: "game_users", required: false },
          ],
          where: { token: token, status: 1 },
        });
      }

      resData.status = true;
      resData.data = gameDetails;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameUserCreate: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token, bet_amount, playerNo } = req.body;

    if (!token) {
      resData.message = "No Token Found";
      res.status(400).json(resData);
      return;
    }

    try {
      const gameDetails = await gameModel.findOne({
        include: [
          {
            model: gameUserModel,
            as: "game_users",
            where: { status: 1 },
            required: false,
          },
        ],
        where: { token: token, status: 1 },
        sort: [["id", "DESC"]],
      });

      if (!gameDetails) {
        resData.message = "Token Not Valid";
        res.status(400).json(resData);
        return;
      }

      let userIn = false;
      let playerIn = false;

      gameDetails.game_users.forEach((user) => {
        if (user.user_id == req.user.user_id) {
          userIn = true;
        }

        if (user.player_no == playerNo) {
          playerIn = true;
        }
      });

      if (userIn) {
        resData.message = "User Already In Game";
        resData.status = true;
        res.status(400).json(resData);
        return;
      } else if (playerIn) {
        resData.message = "Player Already In Game";
        resData.status = true;
        res.status(400).json(resData);
        return;
      } else {
        const val = {
          user_id: req.user.user_id,
          game_id: gameDetails.id,
          bet_amount: bet_amount,
          player_no: playerNo,
        };

        const balanceRes = await callApi("get", "/get_cash_balance", {
          user_id: req.user.user_id,
        });
        const currentBalance = parseFloat(balanceRes?.data?.balance || 0);

        if (currentBalance < bet_amount)
          return sendResponse(res, { message: "Insufficient Balance" });

        const cutRes = await callApi("post", "/adjust_cash_balance", {
          user_id: req.user.user_id,
          amount: bet_amount,
          type: CASH_OPERATION.CUT,
          gateway: "game_join",
        });

        if (!cutRes.status)
          return sendResponse(res, { message: "Failed to deduct balance" });
        await gameUserModel.create({
          user_id: req.user.user_id,
          game_id: gameDetails.id,
          bet_amount,
          player_no: playerNo,
        });
        sendResponse(res, {
          status: true,
          message: `Game User Created & ₹${bet_amount} deducted`,
          code: 200,
        });
      }
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameBuyCoin: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { gameUserId, amount } = req.body;

    const balanceRes = await callApi("get", "/get_cash_balance", {
      user_id: req.user.user_id,
    });
    const currentBalance = parseFloat(balanceRes?.data?.balance || 0);

    if (currentBalance < amount)
      return sendResponse(res, { message: "Insufficient Balance" });

    try {
      const cutRes = await callApi("post", "/adjust_cash_balance", {
        user_id: req.user.user_id,
        amount,
        type: CASH_OPERATION.CUT,
        gateway: "buy_coin",
      });

      if (!cutRes.status)
        return sendResponse(res, { message: "Failed to adjust balance" });

      let gameUser = await gameUserModel.findOne({
        where: {
          id: gameUserId,
          user_id: req.user.user_id,
        },
      });

      if (!gameUser) {
        resData.message = "Game User Not Found";
        res.status(400).json(resData);
        return;
      }

      let bet_amount = gameUser.bet_amount * 1 + amount;

      gameUserModel
        .update({ bet_amount: bet_amount }, { where: { id: gameUserId } })
        .then((result) => {
          updateGameUserBalanace();

          resData.status = true;
          resData.message = `Game Balance Updated`;
          res.status(200).json(resData);
        });
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameUserQuit: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { game_id } = req.body;

    try {
      const gameUser = await gameUserModel.findOne({
        where: {
          user_id: req.user.user_id,
          game_id: game_id,
          status: 1,
        },
      });
      if (!gameUser)
        return sendResponse(res, { message: "Game User Not Found" });

      const addRes = await callApi("post", "/adjust_cash_balance", {
        user_id: req.user.user_id,
        amount: gameUser.bet_amount,
        type: "add_cash",
        gateway: "game_quit",
      });

      if (!addRes.status)
        return sendResponse(res, { message: "Refund failed, please retry" });

      // Update User
      await gameUserModel.update(
        { withdraw_amount: gameUser.bet_amount, status: 2 },
        { where: { id: gameUser.id } }
      );

      sendResponse(res, {
        status: true,
        message: `User quit & ₹${gameUser.bet_amount} refunded`,
        code: 200,
      });
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameUserPeerUpdate: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token, peer_id } = req.body;

    try {
      const gameDetails = await gameModel.findOne({
        include: [{ model: gameUserModel, as: "game_users", required: false }],
        where: { token: token, status: 1 },
        sort: [["id", "DESC"]],
      });

      if (!gameDetails) {
        resData.message = "Token Not Valid";
        res.status(400).json(resData);
        return;
      }

      await gameUserModel.update(
        {
          peer_id: peer_id,
        },
        {
          where: {
            user_id: req.user.user_id,
            game_id: gameDetails.id,
            status: 1,
          },
        }
      );

      resData.status = true;
      resData.message = `Game User Peer Updated`;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  gameUserSittingUpdate: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token, sitting_out_status } = req.body;

    try {
      const gameDetails = await gameModel.findOne({
        include: [{ model: gameUserModel, as: "game_users", required: false }],
        where: { token: token, status: 1 },
        sort: [["id", "DESC"]],
      });

      if (!gameDetails) {
        resData.message = "Token Not Valid";
        res.status(400).json(resData);
        return;
      }

      await gameUserModel.update(
        {
          sitting_out: sitting_out_status ? 1 : 0,
        },
        {
          where: {
            user_id: req.user.user_id,
            game_id: gameDetails.id,
            status: 1,
          },
        }
      );

      resData.status = true;
      resData.message = `Game User Peer Updated`;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  //
  publicGameList: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    try {
      let gameList = await gameModel.findAll({
        where: { status: 1 },
        sort: [["id", "DESC"]],
      });

      resData.status = true;
      resData.data = gameList;
      res.status(200).json(resData);
    } catch (err) {
      console.log("error", err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },
};
