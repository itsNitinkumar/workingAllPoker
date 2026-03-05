const bcrypt = require("bcrypt");

const common = require("../../middleware/common");
const db = require("../../models").default;
const {
  gameModel,
  gameUserModel,
  gameChatsModel,
  gameEmojisModel,
} = require("../../models");

module.exports = {
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
};
