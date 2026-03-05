const axios = require("axios");
const qs = require("qs");

const API_BASE_URL = process.env.API_BASE_URL || "http://80.209.238.145:214/api";
const API_KEY = process.env.RESTFUL_API_KEY;

const sendResponse = (
  res,
  { status = false, data = {}, message = "", code = 400, ...extra }
) => {
  return res.status(code).json({ status, data, message, ...extra });
};

const callApi = async (
  method,
  endpoint,
  payload = {},
  useFormUrlEncoded = true
) => {
  try {
    const options = {
      method,
      url: `${API_BASE_URL}${endpoint}`,
      headers: {
        "X-API-KEY": API_KEY,
      },
    };

    if (useFormUrlEncoded) {
      options.headers["Content-Type"] = "application/x-www-form-urlencoded";
      if (method.toLowerCase() === "get") {
        options.url += "?" + qs.stringify(payload);
      } else {
        options.data = qs.stringify(payload);
      }
    } else {
      options.headers["Content-Type"] = "application/json";
      if (method.toLowerCase() === "get") {
        options.params = payload;
      } else {
        options.data = payload;
      }
    }

    console.log("Method:", method, "Endpoint:", endpoint, "Payload:", payload);
    const response = await axios(options);
    console.log(
      "Reponse From API : ",
      response.data.message ?? response.message
    );
    // console.log(
    //   "Reponse From API : ",
    //   response.data.message ?? response.message
    // );
    return response.data;
  } catch (error) {
    console.error("API call failed:", error.response?.data || error.message);
    return {
      status: false,
      message: error.response?.data?.message || "External API Error",
      data: error.response?.data || {},
      code: error.response?.status || 500,
    };
  }
};

module.exports = { sendResponse, callApi };
