const jwt = require("jsonwebtoken");

module.exports = {
  generateJwt: function (payload) {
    const expiry = new Date();
    expiry.setDate(expiry.getDate() + 7);
    payload.exp = parseInt(expiry.getTime() / 1000);
    return jwt.sign(payload, "PUR$VI&W");
  },
  validateJwt: function (token) {
    try {
      const decoded = jwt.decode(token);
      return decoded;
    } catch (err) {
      return "error";
    }
  },
};
