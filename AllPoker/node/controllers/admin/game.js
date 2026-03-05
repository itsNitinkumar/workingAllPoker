const bcrypt = require("bcrypt");

const common = require("../../middleware/common");
const db = require("../../models").default;
const { gameModel } = require("../../models");

module.exports = {
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
