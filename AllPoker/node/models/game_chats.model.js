module.exports = (db) => {
    const sequelize = db.sequelize;
    const Sequelize = db.Sequelize;

    const game_chats = sequelize.define(
        "game_chats",
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
            from_user_id: {
                type: Sequelize.INTEGER,
                field: "from_user_id",
                allowNull: true,
            },
            from_id: {
                type: Sequelize.INTEGER,
                field: "from_id",
                allowNull: true,
            },
            from_player_no: {
                type: Sequelize.INTEGER,
                field: "from_player_no",
                allowNull: true,
            },
            from_type: {
                type: Sequelize.STRING,
                field: "from_type",
                allowNull: true,
            },
            chat_type: {
                type: Sequelize.STRING,
                field: "type",
                allowNull: true,
            },
            message: {
                type: Sequelize.TEXT,
                field: "message",
                allowNull: true,
            },
            emoji_id: {
                type: Sequelize.INTEGER,
                field: "emoji_id",
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

    game_chats.belongsTo(db.gameModel, { as: 'game', foreignKey: 'game_id' });
    game_chats.belongsTo(db.gameUserModel, { as: 'game_user', foreignKey: 'game_user_id' });
    game_chats.belongsTo(db.userModel, { as: 'from_user', foreignKey: 'from_id' });
    game_chats.belongsTo(db.gameEmojisModel, { as: 'game_emoji', foreignKey: 'emoji_id' });

    return game_chats;
};
