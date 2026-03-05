module.exports = (db) => {
    const sequelize = db.sequelize;
    const Sequelize = db.Sequelize;

    const game_details = sequelize.define(
        "game_details",
        {
            id: {
                type: Sequelize.INTEGER,
                field: "id",
                primaryKey: true,
                autoIncrement: true,
                allowNull: true,
            },
            game_id: {
                type: Sequelize.INTEGER,
                field: "game_id",
                allowNull: true,
            },
            winner_id: {
                type: Sequelize.INTEGER,
                field: "winner_id",
                allowNull: true,
            },
            win_amount: {
                type: Sequelize.STRING,
                field: "win_amount",
                allowNull: true,
            },
            game_data: {
                type: Sequelize.JSON,
                field: "game_data",
                allowNull: true,
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

    return game_details;
};