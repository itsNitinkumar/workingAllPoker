const common = require("../common");
const db = require("../../models");

const users = db.users;

module.exports = (req, res, next) => {
  const resdata = {
    status: false,
    data: {},
    message: "",
  };

  try {
    if (req.headers.authorizationdealer) {
      const token = req.headers.authorizationdealer.split(" ")[1];
      const decodedToken = common.validateJwt(token);
      const user_id = decodedToken.user_id;
      if (user_id) {
        req.user = decodedToken;
        next();
      }
    } else {
      resdata.message = "You dont have authorisation.";
      res.status(401).json(resdata);
      return;
    }
  } catch (err) {
    resdata.message = "Unauthorised";
    res.status(401).json(resdata);
    return;
  }
};
