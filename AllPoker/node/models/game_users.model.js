module.exports = (db) => {
  const sequelize = db.sequelize;
  const Sequelize = db.Sequelize;

  const game_users = sequelize.define(
    "game_users",
    {
      id: {
        type: Sequelize.INTEGER,
        field: "id",
        primaryKey: true,
        autoIncrement: true,
        allowNull: true,
      },
      user_id: {
        type: Sequelize.INTEGER,
        field: "user_id",
        allowNull: true,
      },
      game_id: {
        type: Sequelize.INTEGER,
        field: "game_id",
        allowNull: true,
      },
      player_no: {
        type: Sequelize.INTEGER,
        field: "player_no",
        allowNull: true,
      },
      bet_amount: {
        type: Sequelize.STRING,
        field: "bet_amount",
        allowNull: true,
      },
      withdraw_amount: {
        type: Sequelize.STRING,
        field: "withdraw_amount",
        allowNull: true,
      },
      game_play: {
        type: Sequelize.JSON,
        field: "game_play",
        allowNull: true,
      },
      peer_id: {
        type: Sequelize.STRING,
        field: "peer_id",
        allowNull: true,
      },
      sitting_out: {
        type: Sequelize.INTEGER,
        field: "sitting_out",
        allowNull: true,
        defaultValue: 0,
      },
      missed_smallblind: {
        type: Sequelize.INTEGER,
        field: "missed_smallblind",
        allowNull: true,
        defaultValue: 0,
      },
      status: {
        type: Sequelize.INTEGER,
        field: "status",
        allowNull: true,
        defaultValue: 1,
      },
    },
    { paranoid: true }
  );

  return game_users;
};
