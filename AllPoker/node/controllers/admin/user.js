const bcrypt = require("bcrypt");

const common = require("../../middleware/common");
const db = require("../../models").default;
const { CASH_OPERATION } = require("../../utils/constants");
const { callApi } = require("../../services/apiService");

module.exports = {
  // Linking Api
  publicUserLogin: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { email, cash_balance, game_token, linking_id } = req.body;

    if (!email) {
      resData.message = "Email is required";
      res.status(400).json(resData);
      return;
    }
    if (!cash_balance) {
      resData.message = "Cash Balance is required";
      res.status(400).json(resData);
      return;
    }
    if (!game_token) {
      resData.message = "Game Token is required";
      res.status(400).json(resData);
      return;
    }
    if (!linking_id) {
      resData.message = "Linking Id is required";
      res.status(400).json(resData);
      return;
    }
    try {
      //  Check if user exists via PHP REST API
      const userCheck = await callApi("GET", "/user_email", { email });

      if (!userCheck.status) {
        //  Create new user (if not found)
        const password = await bcrypt.hash("demo@pureviewUser", 10);
        const newUser = await callApi("POST", "/register", {
          first_name: email.split("@")[0],
          last_name: "",
          email_address: email,
          password: password,
        });

        if (!newUser.status) {
          return res.status(400).json({
            status: false,
            message: "Unable to create user via API",
          });
        }

        // Fetch new user by email again
        const createdUser = await callApi("GET", "/user_email", { email });
        await updateCashAndSend(createdUser.data);
      } else {
        // 3️⃣ If exists → adjust cash and send data
        await updateCashAndSend(userCheck.data);
      }

      // Function to update cash + send response
      async function updateCashAndSend(user) {
        await callApi("POST", "/adjust_cash_balance", {
          user_id: user.id,
          amount: cash_balance,
          type: CASH_OPERATION.ADD,
          gateway: "linking_api",
        });

        const payload = {
          user_id: user.id,
          user_email: user.email,
          username: user.username,
          user_name: user.name,
        };

        const token = common.generateJwt(payload);
        resData.status = true;
        resData.token = token;
        resData.link = `https://game.allcardroom.com/linkeduser?token=${game_token}&user_token=${token}`;
        resData.message = "User Login Successfully";
        res.status(200).json(resData);
      }
    } catch (err) {
      console.error("Error in publicUserLogin:", err);
      res.status(500).json({
        status: false,
        message: "Something went wrong while linking user",
      });
    }
  },
};
