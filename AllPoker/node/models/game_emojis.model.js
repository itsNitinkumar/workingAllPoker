module.exports = (db) => {
    const sequelize = db.sequelize;
    const Sequelize = db.Sequelize;

    const game_emojis = sequelize.define(
        "game_emojis",
        {
            id: {
                type: Sequelize.INTEGER,
                field: "id",
                primaryKey: true,
                autoIncrement: true,
                allowNull: true,
            },
            name: {
                type: Sequelize.STRING,
                field: "name",
                allowNull: true,
            },
            file_name: {
                type: Sequelize.STRING,
                field: "file_name",
                allowNull: true,
            },
            thumb: {
                type: Sequelize.STRING,
                field: "thumb",
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

    return game_emojis;
};