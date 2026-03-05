const bcrypt = require("bcrypt");

const common = require("../../middleware/common");
const db = require("../../models").default;

// JABRA
// import { init, RequestedBrowserTransport } from "@gnaudio/jabra-js";

// (async () => {
//   const { init, RequestedBrowserTransport } = await import("@gnaudio/jabra-js");
// })();

module.exports = {
  login: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { username, password } = req.body;

    if (!username || !password) {
      resData.message = "Username Or Password can not be blank";
      res.status(400).json(resData);
      return;
    }

    try {
      const apiLoginResponse = await callApi("post", "/login", {
        username: username,
        password: password,
      });
      if (!apiLoginResponse.status) {
        resData.message =
          apiLoginResponse.message || "Invalid username or password";
        return res.status(400).json(resData);
      }
      const user = apiLoginResponse.data;
      if (parseInt(user.role) !== 2) {
        resData.message = "Access restricted. Only role 2 users are allowed.";
        return res.status(403).json(resData);
      }
      const payload = {
        user_id: user.id,
        email: user.email_address,
        username: user.username,
        name: `${user.first_name || ""} ${user.last_name || ""}`.trim(),
      };

      const token = common.generateJwt(payload);
      resData.status = true;
      resData.data = user;
      resData.message = "User Login Successfully";
      resData.token = token;

      return res.status(200).json(resData);
    } catch (err) {
      console.log(err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  profileDetails: async function (req, res) {
    const resData = { status: false, data: {}, message: "" };

    try {
      const userId = req.user?.user_id;
      if (!userId) {
        resData.message = "User ID missing in token";
        return res.status(401).json(resData);
      }

      //  Fetch User
      let apiResponse = await callApi("get", "/user", { id: userId });
      if (!apiResponse.status || !apiResponse.data) {
        resData.message = "User not found";
        return res.status(404).json(resData);
      }

      let userdetail = apiResponse.data;

      //  Fetch balance from /get_cash_balance
      const balanceResponse = await callApi("get", "/get_cash_balance", null, {
        user_id,
      });
      let currentBalance = 0;
      if (balanceResponse?.status) {
        currentBalance = parseFloat(balanceResponse.data.data.balance || 0);
      }

      // Initialize balance if 0
      if (currentBalance === 0) {
        console.log(`⚡ Initializing cash balance for user ${userId}`);

        const adjustResponse = await callApi("post", "/adjust_cash_balance", {
          user_id: userId,
          amount: 10000,
          type: "add_cash",
          gateway: "system_init",
        });

        if (adjustResponse.status) {
          const refreshed = await callApi("get", "/get_cash_balance", {
            user_id: userId,
          });
          if (refreshed.status)
            currentBalance = parseFloat(refreshed.data.balance || 10000);
        }
      }
      resData.status = true;
      resData.message = "Profile fetched successfully";
      resData.data = {
        ...userdetail,
        cash_balance: currentBalance,
      };
      const data = {
        id: resData.id,
        username: resData.username,
        email: resData.email,
        first_name: resData.first_name,
        last_name: resData.last_name,
        role: resData.role,
        cash_balance: currentBalance,
      };
      return res.status(200).json(data);
    } catch (err) {
      console.error("ProfileDetails Error:", err);
      resData.message = "Please try again";
      return res.status(500).json(resData);
    }
  },
};
