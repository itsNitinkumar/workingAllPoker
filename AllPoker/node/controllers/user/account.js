const bcrypt = require("bcrypt");
const jwt = require("jsonwebtoken");
const common = require("../../middleware/common");
const { userModel } = require("../../models");
const { sendResponse, callApi } = require("../../services/apiService");

module.exports = {
  login: async (req, res) => {
    const { username, password } = req.body;
    if (!username || !password)
      return sendResponse(res, {
        message: "Username or password cannot be blank",
      });

    try {
      const apiResponse = await callApi("post", "/login", {
        username,
        password,
      });
      if (!apiResponse.status)
        return sendResponse(res, {
          message: apiResponse.message || "Invalid credentials",
        });

      // Optional: generate your own JWT
      const token = common.generateJwt({
        user_id: apiResponse.data.id,
        email: apiResponse.data.email_address,
      });

      sendResponse(res, {
        status: true,
        data: apiResponse.data,
        message: "Login successful",
        code: 200,
        token,
      });
    } catch (err) {
      console.error(err.response?.data || err.message);
      sendResponse(res, {
        message:
          err.response?.data.message ||
          err.message ||
          "API authentication failed",
        code: 500,
      });
    }
  },

  register: async (req, res) => {
    const { first_name, last_name, email_address, password } = req.body;
    if (!first_name || !last_name || !email_address || !password) {
      return sendResponse(res, { message: "All fields are required" });
    }

    try {
      const apiResponse = await callApi("post", "/register", {
        first_name,
        last_name,
        email_address,
        password,
      });
      sendResponse(res, {
        status: apiResponse.status,
        message: apiResponse.message || "Registration completed",
        data: apiResponse.data || {},
        code: apiResponse.status ? 200 : 400,
      });
    } catch (err) {
      console.error(err.response?.data || err.message);
      sendResponse(res, { message: "API registration failed", code: 500 });
    }
  },

  profileDetails: async (req, res) => {
    try {
      const { user_id } = req.user;
      if (!user_id)
        return sendResponse(res, { message: "User ID is required" });

      const apiResponse = await callApi("get", "/user", { id: user_id });
      if (!apiResponse.status)
        return sendResponse(res, { message: "User not found" });
      const user = apiResponse.data;
      const balanceResponse = await callApi("get", "/get_cash_balance", {
        user_id: user_id,
      });

      let currentBalance = 0;
      if (balanceResponse?.status) {
        currentBalance = parseFloat(balanceResponse.data?.balance || 0);
      }

      // 3️⃣ Initialize if balance not found or zero
      if (currentBalance === 0) {
        console.log(`⚡ Initializing cash balance for user ${user_id}`);

        const adjustResponse = await callApi("post", "/adjust_cash_balance", {
          user_id: user_id,
          amount: 10000,
          type: "add_cash",
          gateway: "system_init",
        });

        if (adjustResponse?.status) {
          const refreshed = await callApi("get", "/get_cash_balance", {
            user_id: user_id,
          });
          if (refreshed?.status)
            currentBalance = parseFloat(refreshed.data?.balance || 10000);
        }
      }

      // 4️⃣ Combine user details with balance
      const profileData = {
        id: user.id,
        username: user.username,
        email: user.email,
        first_name: user.first_name,
        last_name: user.last_name,
        role: user.role,
        cash_balance: currentBalance,
      };

      // 5️⃣ Send response
      return sendResponse(res, {
        status: true,
        data: profileData,
        message: "Profile fetched successfully",
        code: 200,
      });
    } catch (err) {
      console.error(err.response?.data || err.message);
      sendResponse(res, { message: "Failed to fetch profile", code: 500 });
    }
  },

  logout: async (req, res) => {
    const { token } = req.body;
    if (!token) return sendResponse(res, { message: "Token is required" });

    try {
      const apiResponse = await callApi("get", "/logout", { token });
      sendResponse(res, {
        status: apiResponse.status,
        message: apiResponse.message || "Logged out successfully",
        code: apiResponse.status ? 200 : 400,
      });
    } catch (err) {
      console.error(err.response?.data || err.message);
      sendResponse(res, { message: "Logout failed", code: 500 });
    }
  },
  validateSession: async (req, res) => {
    const { token } = req.body;
    if (!token) return sendResponse(res, { message: "Token is required" });

    try {
      const apiResponse = await callApi("get", "/validate_session", { token });
      sendResponse(res, {
        status: apiResponse.status,
        message: apiResponse.status ? "Session valid" : "Invalid session",
        code: apiResponse.status ? 200 : 401,
      });
    } catch (err) {
      console.error(err.response?.data || err.message);
      sendResponse(res, { message: "Session validation failed", code: 500 });
    }
  },
  // Linking Api
  publicUserLogin: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { email, cash_balance, game_token, linking_id } = req.body;

    try {
      // Check User
      userModel.findOne({ where: { email: email } }).then((result) => {
        if (result == null) {
          createNewUser();
        } else {
          updateUser(result);
        }
      });

      // Create New User
      function createNewUser() {
        bcrypt.hash("demo@pureviewUser", 10, function (err, hash) {
          const pass = hash;

          const val = {
            email,
            password: pass,
            role: "user",
            cash_balance: cash_balance,
            linking_id: linking_id,
          };

          userModel.create(val).then((result) => {
            console.log(result.id);
            findUser(result.id);
          });
        });
      }

      //
      async function findUser(id) {
        let result = await userModel.findOne({ where: { id: id } });

        sendUserData(result);
      }

      //
      async function updateUser(result) {
        await userModel.update(
          { cash_balance: cash_balance },
          {
            where: { id: result.id },
          }
        );

        sendUserData(result);
      }

      function sendUserData(result) {
        const _payload = {
          user_id: result.id,
          user_email: result.email,
          username: result.username,
          user_name: result.name,
        };

        const token = common.generateJwt(_payload);

        resData.status = true;
        resData.token = token;
        resData.link = `https://game.allcardroom.com/linkeduser?token=${game_token}&user_token=${token}`;
        resData.message = "User Login Successfully";
        res.status(200).json(resData);
        return;
      }
    } catch (err) {
      console.log(err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  checkLinkingToken: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { token, user_token } = req.body;

    console.log(token, user_token);

    try {
      const decoded = jwt.decode(user_token);

      console.log("decoded", decoded);

      if (decoded.user_id && decoded.user_email) {
        let result = await userModel.findOne({
          where: {
            id: decoded.user_id,
            email: decoded.user_email,
          },
        });

        if (!result) {
          resData.message = "No User Found";
          res.status(400).json(resData);
          return;
        }

        resData.data = {
          user_id: result.id,
          role: result.role,
          email: result.email,
        };

        resData.status = true;
        resData.token = user_token;
        resData.message = "User Login Successfully";
        res.status(200).json(resData);
        return;
      } else {
        resData.message = "User Not Valid";
        res.status(400).json(resData);
        return;
      }
    } catch (err) {
      console.log(err);
      resData.message = "Please try again";
      res.status(500).json(resData);
      return;
    }
  },

  // TESTING
  testing: async function (req, res) {
    const resData = {
      status: false,
      data: {},
      message: "",
    };

    const { init, RequestedBrowserTransport, JabraError, ErrorType } =
      await import("@gnaudio/jabra-js");

    const config = {
      transport: RequestedBrowserTransport.WEB_HID,
      partnerKey: "0123-456789ab-cdef-0123-4567-89abcdef0123",
      appId: "pure-view",
      appName: "Pure View",
      logger: {
        write(logEvent) {
          if (logEvent.level === "error") {
            console.log(logEvent.message, logEvent.layer);
          }
          // Ignore messages with other log levels
        },
      },
    };

    // Initialize Jabra library using the optional config object
    const jabraCore = await init(config);

    console.log("jabraCore => ", jabraCore);
  },
};
