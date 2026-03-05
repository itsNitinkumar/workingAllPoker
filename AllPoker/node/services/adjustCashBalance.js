const { CASH_OPERATION } = require("../utils/constants");
const { sendResponse, callApi } = require("./apiService");

exports.adjustCashBalance = async (req, res) => {
  try {
    const { user_id, amount, type, gateway = "manual" } = req.body;

    // ✅ Validation
    if (!user_id || !amount || !type) {
      return sendResponse(res, {
        message: "Invalid request data",
        status: false,
        code: 400,
      });
    }

    if (![CASH_OPERATION.ADD, CASH_OPERATION.CUT].includes(type)) {
      return sendResponse(res, {
        message: "Invalid operation type",
        status: false,
        code: 400,
      });
    }
    const response = await callApi("POST", "/adjust-cash-balance", {
      user_id,
      amount,
      type,
      gateway,
    });

    return sendResponse(res, {
      status: response.status,
      message: response.message || "Operation successful",
      data: response.data || {},
      code: response.status ? 200 : 400,
    });
  } catch (error) {
    console.error("adjustCashBalance error:", error);
    return sendResponse(res, {
      message: "Internal server error",
      status: false,
      code: 500,
    });
  }
};
