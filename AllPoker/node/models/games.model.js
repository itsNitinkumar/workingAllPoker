module.exports = (db) => {
  const sequelize = db.sequelize;
  const Sequelize = db.Sequelize;

  const games = sequelize.define(
    "games",
    {
      id: {
        type: Sequelize.INTEGER,
        field: "id",
        primaryKey: true,
        autoIncrement: true,
        allowNull: true,
      },
      winner_id: {
        type: Sequelize.STRING,
        field: "winner_id",
        allowNull: true,
      },
      bet_amount: {
        type: Sequelize.STRING,
        field: "bet_amount",
        allowNull: true,
      },
      token: {
        type: Sequelize.STRING,
        field: "token",
        allowNull: true,
      },
      game_play: {
        type: Sequelize.JSON,
        field: "game_play",
        allowNull: true,
      },
      table_game: {
        type: Sequelize.INTEGER,
        field: "table_game",
        allowNull: true,
        defaultValue: 1
      },
      status: {
        type: Sequelize.INTEGER,
        field: "status",
        allowNull: true,
        defaultValue: 1,
      },
    },
    {
      paranoid: true,
      // alter: true,
    }
  );

  games.hasMany(db.gameUserModel, { as: 'game_users', foreignKey: 'game_id' });

  return games;
};
