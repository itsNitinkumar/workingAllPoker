module.exports = (db) => {
  const sequelize = db.sequelize;
  const Sequelize = db.Sequelize;

  const User = sequelize.define(
    "users",
    {
      id: {
        type: Sequelize.INTEGER,
        field: "id",
        primaryKey: true,
        autoIncrement: true,
        allowNull: true,
      },
      username: {
        type: Sequelize.STRING,
        field: "username",
        allowNull: true,
      },
      email: {
        type: Sequelize.STRING,
        field: "email",
        allowNull: true,
      },
      first_name: {
        type: Sequelize.STRING,
        field: "first_name",
        allowNull: true,
      },
      last_name: {
        type: Sequelize.STRING,
        field: "last_name",
        allowNull: true,
      },
      password: {
        type: Sequelize.STRING,
        field: "password",
        allowNull: true,
      },
      role: {
        type: Sequelize.INTEGER,
        field: "role",
        allowNull: true,
      },
      cash_balance: {
        type: Sequelize.INTEGER,
        field: "cash_balance",
        allowNull: true,
        defaultValue: 0
      },
      status: {
        type: Sequelize.INTEGER,
        field: "status",
        allowNull: true,
        defaultValue: 1,
      },
      last_login: {
        type: Sequelize.DATE,
        field: "last_login",
        allowNull: true,
      },
      pass_reset: {
        type: Sequelize.STRING,
        field: "pass_reset",
        allowNull: true,
      },
      country_id: {
        type: Sequelize.INTEGER,
        field: "country_id",
        allowNull: true
      },
      linking_id: {
        type: Sequelize.INTEGER,
        field: "linking_id",
        allowNull: true
      }
    },
    {
      paranoid: true,
      alter: true,
    }
  );

  return User;
};
