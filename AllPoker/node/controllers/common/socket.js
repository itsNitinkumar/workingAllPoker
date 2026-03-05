const Hand = require("pokersolver").Hand;
const mqtt = require("mqtt");
const mqttClient = mqtt.connect("mqtt://80.209.238.145");

const { get } = require("https");
const RandomOrg = require("random-org");
var random = new RandomOrg({ apiKey: "fd7efc66-9b44-4240-b539-1b9393e2b721" });

// random.generateIntegers({ n: 5, min: 1, max: 52, replacement: false }).then(function (result) {
//     console.log("random", result);
// })

const {
  gameUserModel,
  gameDetailsModel,
  gameChatsModel,
} = require("../../models");
const { type } = require("os");
const { callApi } = require("../../services/apiService");
const cardValues = require("./rfid_values").cards;

mqttClient.on("connect", (data) => {
  mqttClient.subscribe("/to/my/topic", (err) => {
    if (!err) {
      console.log("subscribed");
      mqttClient.publish("/to/my/topic", `Hello mqtt from localhost`);
    } else {
      console.log("err", err);
    }
  });
});

const users = {};
const tables = {};
const gameDetails = {};
const peerIds = {};
const interval = {};
let tableCards = [];
let mqttData = {};
let demoMqttData = {};
let serveCards = [];

let randomCardData = {};

let restart = false;

// 06.02.2024 - Started Sitting Out Logic
let removeUserCount = {};

module.exports = function (io) {
  if (!restart) {
    setTimeout(() => {
      // console.log("restart", restart)
      io.emit("reloadPage", true);
    }, 5000);

    restart = true;
  }

  mqttClient.on("message", (topic, message) => {
    let msg = message.toString();
    let data = {};
    let muxReads = {};

    try {
      data = JSON.parse(msg);
    } catch (e) {
      data = {};
    }

    // console.log("data", data);
    if (data.reads) {
      // console.log("data.reads.length", data.reads.length, new Date())
      if (mqttData[11]) {
        // console.log("mqttData[11].length", mqttData[11].length, new Date())
      }
    }

    if (data.reads) {
      data.reads.forEach((item) => {
        // Demo Checking
        if (!mqttData[item.Mux1]) {
          mqttData[item.Mux1] = [];
        }

        let hasMqtt = false;
        mqttData[item.Mux1].forEach((mqtt) => {
          if (mqtt && item) {
            if (mqtt.EPC == item.EPC) {
              hasMqtt = true;
            }
          }
        });

        if (!hasMqtt) {
          mqttData[item.Mux1].push(item);
        }

        muxReads[item.Mux1] = true;
      });
    }

    // Demo Print
    // console.log("mqttData", mqttData);
    // console.log("muxReads", muxReads, new Date());
    io.emit("mqttComplete", mqttData);
  });

  // Random Card Generator
  let getRandomCard = async (gameId) => {
    let randomNum = await random.generateIntegers({
      n: 23,
      min: 1,
      max: 52,
      replacement: false,
    });
    if (!randomNum.random) {
      return;
    }
    if (!randomNum.random.data) {
      return;
    }

    console.log("randomNum", randomNum.random.data);

    let randomArr = randomNum.random.data;
    randomCardData[gameId] = {};

    randomArr.forEach((item) => {
      let added = false;
      let rndNo = item - 1;
      for (let i = 1; i < 12; i++) {
        if (!added) {
          let card = cardValues[rndNo];
          let data = { EPC: card["epc"] };

          if (!randomCardData[gameId][i]) {
            randomCardData[gameId][i] = [];
          }

          if (randomCardData[gameId][i].length < 2 && i < 10) {
            randomCardData[gameId][i].push(data);
            added = true;
          }

          if (randomCardData[gameId][i].length < 5 && i == 11) {
            randomCardData[gameId][i].push(data);
            added = true;
          }
        }
      }
    });

    // console.log("randomCardData[gameId]", randomCardData[gameId])
  };

  // getRandomCard(1)

  io.on("connect", async function onConnect(socket) {
    console.log(`user ${socket.id} connected`);

    socket.on("cameraControlSend", async function (data) {
      socket.broadcast.emit("cameraControlReceive", data);
    });

    socket.on("cameraSettingsSend", async function (data) {
      socket.broadcast.emit("cameraSettingsReceive", data);
    });

    // =================================================
    // Poker Gameplay
    // =================================================
    socket.on("pokerTableGroupJoin", async function (data) {
      try {
        let gameId = data.gameId;
        console.log("pokerTableGroupJoin", data);

        // Join Game
        socket.join(gameId);
        socket.join(`user_${data.userId}`);

        users[socket.id] = data;

        // Send User List
        if (gameDetails[gameId]) {
          io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
          io.to(gameId).emit("peerUserList", peerIds[gameId]);
        }
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("pokerJoinGame", async function (data) {
      try {
        let gameId = data.gameId;
        console.log("pokerJoinGame", data);

        // Join Game
        socket.join(gameId);
        socket.join(`user_${data.userId}`);

        users[socket.id] = data;
        io.to(socket.id).emit("getSocketId", socket.id);

        if (!gameDetails[gameId]) {
          gameDetails[gameId] = {
            id: data.id,
            currentTurnId: null,
            gameId: gameId,
            tableGame: data.tableGame == false ? false : true,
            gameStarted: false,
            straddle: true,
            potRaised: false,
            tableStep: null,
            sbPlayerNo: null,
            step: 1,
            tableStep: null, // 'flop', 'turn', 'river'
            potValue: 0,
            timer: 60,
            serveCards: [],
            winnerDeclared: false,
            checkUserId: null,
            players: [],
            waitingPlayers: [],
          };
        }

        if (!gameDetails[gameId].players) {
          gameDetails[gameId].players = [];
        }

        let playersList = gameDetails[gameId].players;
        if (!gameDetails[gameId].gameStarted) {
          let alreadyIn = false;
          let alreadyInIndex = null;
          gameDetails[gameId].players.forEach((player, index) => {
            if (player.user_id == data.userId) {
              alreadyIn = true;
              alreadyInIndex = index;
              gameDetails[gameId].players[index].socketId = socket.id;
              gameDetails[gameId].players[index].disconnected = false;
            }
          });

          if (!alreadyIn) {
            playersList.push({
              socketId: socket.id,

              user_id: data.userId,
              playerNo: data.playerNo,
              beginingAmount: data.betAmount,
              betAmount: data.betAmount,
              sittingOut: data.sittingOut,
              email: data.email,
              username: data.username,
              first_name: data.first_name,
              last_name: data.last_name,

              peerId: null,
              myCards: [],
              callValue: 0,
              currentTurn: null,
              currentUserIndex: null,
              handStatus: null,
              nextTurn: "blind",
              lastTurn: "",
              turnStatus: "",
              turn: false,
              fold: false,
              winner: false,
              disconnected: false,
              timedOut: false,
            });
          } else {
            if (alreadyInIndex != null) {
              gameDetails[gameId].players[alreadyInIndex].betAmount =
                data.betAmount;
              gameDetails[gameId].players[alreadyInIndex].beginingAmount =
                data.betAmount;
            }
          }

          playersList.sort((a, b) => a.playerNo - b.playerNo);

          // Set Current Player Value
          gameDetails[gameId].players = playersList;
        } else {
          let alreadyIn = false;
          gameDetails[gameId].players.forEach((player, index) => {
            if (player.user_id == data.userId) {
              alreadyIn = true;
              gameDetails[gameId].players[index].socketId = socket.id;
              gameDetails[gameId].players[index].disconnected = false;
            }
          });

          if (!alreadyIn) {
            gameDetails[gameId].waitingPlayers.push({
              user_id: data.userId,
              playerNo: data.playerNo,
              callValue: 0,
              currentTurn: null,
              socketId: socket.id,
              currentUserIndex: null,
              nextTurn: "blind",
              lastTurn: "",
              turn: false,
              fold: false,
              winner: false,
            });
          }
        }

        if (!gameDetails[gameId].gameStarted) {
          await setUserTurn(gameId);
        }

        if (gameDetails[gameId].players.length <= 1) {
          gameDetails[gameId].cardMessage = "Waiting for more players.....";
        } else {
          gameDetails[gameId].cardMessage = "";
        }

        // Send User List
        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("peerUserList", peerIds[gameId]);

        if (!interval[gameId]) {
          interval[gameId] = setInterval(function () {
            if (gameDetails[gameId].gameStarted) {
              // All In Counter
              let totalCount = 0;
              let allInCount = 0;
              let currentUserIndex = null;
              gameDetails[gameId].players.forEach((player, index) => {
                if (!player.fold && !player.sittingOut) {
                  totalCount++;
                  if (player.allIn) {
                    allInCount++;
                  }
                }
                if (
                  player.turn &&
                  player.nextTurn == "check" &&
                  !gameDetails[gameId].winnerDeclared
                ) {
                  currentUserIndex = index;
                }
              });

              if (allInCount >= totalCount - 1 && currentUserIndex != null) {
                commonCheck(gameId, currentUserIndex);
              }

              // Timeout Counter
              if (gameDetails[gameId].timer <= 0) {
                gameDetails[gameId].timer = 60;

                setUserTimeout(gameId);
              } else {
                gameDetails[gameId].timer--;
              }
            } else {
              gameDetails[gameId].timer = 60;

              if (gameDetails[gameId].players.length == 0) {
                clearInterval(interval[gameId]);
                delete interval[gameId];
              }
            }
          }, 1000);
        }
      } catch (error) {
        console.log(error);
      }
    });

    socket.on("pokerBlindSend", async function (data) {
      try {
        let gameId = users[socket.id].gameId;

        console.log("pokerBlindSend", users[socket.id]);

        let totalPlayers = 0;
        if (gameDetails[gameId].players) {
          gameDetails[gameId].players.forEach((player) => {
            if (!player.sittingOut && !player.fold && !player.disconnected) {
              totalPlayers++;
            }
          });
        }

        if (totalPlayers < 2) {
          io.to(gameId).emit("reloadPage", true);
          return;
        }

        gameDetails[gameId].gameStarted = true;
        gameDetails[gameId].dealerReset = false;

        if (!gameDetails[gameId].tableGame) {
          getRandomCard(gameId);
        }

        if (gameDetails[gameId].noPlayerArr) {
          let removeUserList = await gameUserModel.findAll({
            where: {
              game_id: gameDetails[gameId].id,
              player_no: gameDetails[gameId].noPlayerArr,
              status: 1,
            },
          });

          for (let i = 0; i < removeUserList.length; i++) {
            let gameUser = removeUserList[i];
            let removeIndex = null;
            let joinedGame = false;

            if (gameUser) {
              if (gameUser.missed_smallblind) {
                gameDetails[gameId].players.forEach((player, index) => {
                  if (player.user_id == gameUser.user_id && player.sittingOut) {
                    removeIndex = index;
                  }
                });

                console.log("gameUser", gameUser);

                let updateGameUser = await gameUserModel.update(
                  {
                    withdraw_amount: gameUser.bet_amount,
                    status: 2,
                    missed_smallblind: 2,
                  },
                  { where: { id: gameUser.id } }
                );

                if (updateGameUser) {
                  try {
                    await callApi("POST", "/adjust_cash_balance", {
                      user_id: gameUser.user_id,
                      amount: gameUser.bet_amount,
                      type: CASH_OPERATION.ADD,
                      gateway: "missed_smallblind_refund",
                    });

                    console.log(
                      ` Refunded ${gameUser.bet_amount} to user ${gameUser.user_id} via API`
                    );
                  } catch (apiError) {
                    console.error(" Failed to call PHP API:", apiError.message);
                  }

                  if (removeIndex != null) {
                    gameDetails[gameId].players.splice(removeIndex, 1);
                  }
                }

                io.to(`user_${gameUser.user_id}`).emit("reloadPage", true);
              } else {
                gameUserModel.update(
                  { missed_smallblind: 1 },
                  { where: { id: gameUser.id } }
                );
              }
            }
          }
        }

        let smallBlindUserIndex = await getUserIndex(gameId, socket.id);

        generateBlind(gameId, smallBlindUserIndex);

        io.to(gameId).emit("gestureReceive", {
          type: "Bet",
          playerNo: users[socket.id].playerNo,
        });
      } catch (error) {
        console.log(error);
      }
    });

    let generateBlind = async (gameId, smallBlindUserIndex) => {
      try {
        gameDetails[gameId].sbPlayerNo =
          gameDetails[gameId].players[smallBlindUserIndex].playerNo;
        gameDetails[gameId].currentUserIndex = smallBlindUserIndex;
        gameDetails[gameId].firstCheck = false;

        // Set Small Blind Player Value
        gameDetails[gameId].players[smallBlindUserIndex].turn = false;
        gameDetails[gameId].players[smallBlindUserIndex].smallBlind = true;
        gameDetails[gameId].players[smallBlindUserIndex].lastTurn = "blind";
        gameDetails[gameId].players[smallBlindUserIndex].callValue = 5;
        gameDetails[gameId].players[smallBlindUserIndex].turnStatus =
          "smallBlind";

        // Update Bet Amount
        await updateBetAmount(gameId, smallBlindUserIndex, 5);

        // Set Big Blind Player Value
        let bigBlindUserIndex = await getNextUserIndex(
          gameId,
          smallBlindUserIndex
        );
        gameDetails[gameId].players[bigBlindUserIndex].turn = false;
        gameDetails[gameId].players[bigBlindUserIndex].bigBlind = true;
        gameDetails[gameId].players[bigBlindUserIndex].lastTurn = "blind";
        gameDetails[gameId].players[bigBlindUserIndex].callValue = 10;
        gameDetails[gameId].players[bigBlindUserIndex].turnStatus = "bigBlind";

        // Set Dealer
        let dealerIndex = null;
        dealerIndex = getDealerIndex(smallBlindUserIndex);
        function getDealerIndex(index) {
          console.log("getDealerIndex", index);

          if (bigBlindUserIndex == index) {
            return smallBlindUserIndex;
          }

          if (index == 0) {
            return getDealerIndex(gameDetails[gameId].players.length);
          } else {
            if (
              !gameDetails[gameId].players[index - 1].sittingOut &&
              !gameDetails[gameId].players[index - 1].fold
            ) {
              return index - 1;
            } else {
              return getDealerIndex(index - 1);
            }
          }
        }

        console.log("dealerIndex", dealerIndex);

        if (dealerIndex != null) {
          gameDetails[gameId].players[dealerIndex].dealer = true;
        }

        // Update Bet Amount
        await updateBetAmount(gameId, bigBlindUserIndex, 10);

        // Set Next Turn
        let nextUserIndex = await getNextUserIndex(gameId, bigBlindUserIndex);
        // gameDetails[gameId].players[nextUserIndex].turn = true;
        gameDetails[gameId].players[nextUserIndex].nextTurn = "call";

        // Set Game Value
        gameDetails[gameId].potValue = await getPotValue(gameId);
        gameDetails[gameId].callValue = 10;

        await gameDetailsModel.update(
          { status: 2 },
          {
            where: { game_id: gameDetails[gameId].id, status: 1 },
          }
        );

        let gameData = await gameDetailsModel.create({
          game_id: gameDetails[gameId].id,
          game_data: gameDetails[gameId],
          status: 1,
        });

        gameDetails[gameId].currentTurnId = gameData.id;
        gameDetails[gameId].timer = 60;

        await getMultiplePotValue(gameId);

        // Straddle Logic

        if (gameDetails[gameId].straddle) {
          gameDetails[gameId].players[nextUserIndex].nextTurn = "straddle";
          gameDetails[gameId].players[nextUserIndex].turn = true;
          gameDetails[gameId].timer = 10;
          gameDetails[gameId].cardMessage = "";
        } else {
          // Send Card
          dealHandCards(gameId, nextUserIndex);
        }

        gameDetailsModel.update(
          { game_data: gameDetails[gameId] },
          {
            where: { id: gameDetails[gameId].currentTurnId },
          }
        );

        // Send Game Status
        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("pokerAudio", "blind_bet_call_raise");

        io.to(gameId).emit("pokerTurnPlay", {
          chipsSlide: true,
          playerNo: gameDetails[gameId].players[smallBlindUserIndex].playerNo,
        });
        io.to(gameId).emit("pokerTurnPlay", {
          chipsSlide: true,
          playerNo: gameDetails[gameId].players[bigBlindUserIndex].playerNo,
        });
      } catch (error) {
        console.log(error);
      }
    };

    socket.on("pokerCallSend", async function (data) {
      try {
        console.log("pokerCallSend", data);

        let gameId = users[socket.id].gameId;
        socket.to(gameId).emit("pokerCallReceive", data);

        let currentUserIndex = await getUserIndex(gameId, socket.id);
        let nextUserIndex = await getNextUserIndex(gameId, currentUserIndex);

        // Set Current Player Value
        gameDetails[gameId].players[currentUserIndex].turn = false;
        gameDetails[gameId].players[currentUserIndex].lastTurn = "call";
        gameDetails[gameId].players[currentUserIndex].turnStatus = "call";
        gameDetails[gameId].players[currentUserIndex].callValue =
          data.callValue;
        // gameDetails[gameId].players[currentUserIndex].dealer = false;

        // Update Bet Amount
        await updateBetAmount(gameId, currentUserIndex, data.callValue);

        if (gameDetails[gameId].players[currentUserIndex].betAmount == 0) {
          gameDetails[gameId].players[currentUserIndex].allIn = true;
          gameDetails[gameId].players[currentUserIndex].lastTurn = "allIn";

          io.to(gameId).emit("gestureReceive", {
            type: "AllIn",
            playerNo: users[socket.id].playerNo,
          });
        } else {
          io.to(gameId).emit("gestureReceive", {
            type: "Bet",
            playerNo: users[socket.id].playerNo,
          });
        }

        if (gameDetails[gameId].callValue < data.callValue) {
          gameDetails[gameId].players[currentUserIndex].raiseValue =
            data.callValue - gameDetails[gameId].callValue;
          gameDetails[gameId].firstCheck = true;

          if (data.bet) {
            gameDetails[gameId].players[currentUserIndex].turnStatus = "bet";
          } else {
            gameDetails[gameId].players[currentUserIndex].turnStatus = "raise";
          }

          gameDetails[gameId].potRaised = true;
        } else {
          gameDetails[gameId].players[currentUserIndex].raiseValue = 0;
        }

        // Set Next Player Value
        if (
          gameDetails[gameId].players[nextUserIndex].callValue >=
            data.callValue ||
          gameDetails[gameId].players[nextUserIndex].allIn
        ) {
          gameDetails[gameId].players[nextUserIndex].nextTurn = "check";
          gameDetails[gameId].checkUserId =
            gameDetails[gameId].players[nextUserIndex].user_id;

          if (gameDetails[gameId].potRaised) {
            commonFetchCards(gameId, nextUserIndex);
            gameDetails[gameId].potRaised = false;
          } else {
            gameDetails[gameId].players[nextUserIndex].turn = true;
          }
        } else {
          gameDetails[gameId].callValue = data.callValue;
          gameDetails[gameId].players[nextUserIndex].nextTurn = "call";
          gameDetails[gameId].players[nextUserIndex].turn = true;
        }

        // Set Game Value
        gameDetails[gameId].potValue = await getPotValue(gameId);
        gameDetails[gameId].timer = 60;

        // Get Multiple Pot
        await getMultiplePotValue(gameId);

        // Send Game Status

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("pokerAudio", "blind_bet_call_raise");
        io.to(gameId).emit("pokerTurnPlay", {
          chipsSlide: true,
          playerNo: gameDetails[gameId].players[currentUserIndex].playerNo,
        });

        gameDetailsModel.update(
          { game_data: gameDetails[gameId] },
          {
            where: { id: gameDetails[gameId].currentTurnId },
          }
        );
      } catch (error) {
        console.log(error);
      }
    });

    socket.on("pokerStraddleSend", async function (data) {
      try {
        console.log("pokerStraddleSend", data);

        let gameId = users[socket.id].gameId;
        let currentUserIndex = await getUserIndex(gameId, socket.id);
        let nextUserIndex = await getNextUserIndex(gameId, currentUserIndex);

        gameDetails[gameId].callValue = data.callValue;

        // Set Current Player Value
        gameDetails[gameId].players[currentUserIndex].turn = false;
        gameDetails[gameId].players[currentUserIndex].lastTurn = "straddle";
        gameDetails[gameId].players[currentUserIndex].turnStatus = "straddle";
        gameDetails[gameId].players[currentUserIndex].callValue =
          data.callValue;

        // Update Bet Amount
        await updateBetAmount(gameId, currentUserIndex, data.callValue);

        gameDetails[gameId].players[currentUserIndex].raiseValue =
          data.callValue - gameDetails[gameId].callValue;
        gameDetails[gameId].firstCheck = true;

        // Get Multiple Pot
        await getMultiplePotValue(gameId);

        // Send Game Status
        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("pokerAudio", "blind_bet_call_raise");
        io.to(gameId).emit("pokerTurnPlay", {
          chipsSlide: true,
          playerNo: gameDetails[gameId].players[currentUserIndex].playerNo,
        });

        io.to(gameId).emit("gestureReceive", {
          type: "Bet",
          playerNo: users[socket.id].playerNo,
        });

        dealHandCards(gameId, nextUserIndex);

        gameDetailsModel.update(
          { game_data: gameDetails[gameId] },
          {
            where: { id: gameDetails[gameId].currentTurnId },
          }
        );
      } catch (error) {
        console.log(error);
      }
    });

    socket.on("pokerCheckSend", async function (data) {
      try {
        let gameId = users[socket.id].gameId;
        socket.to(gameId).emit("pokerCallReceive", data);

        let currentUserIndex = await getUserIndex(gameId, socket.id);

        commonCheck(gameId, currentUserIndex);
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("pokerFoldSend", async function (data) {
      try {
        let gameId = users[socket.id].gameId;

        let currentUserIndex = await getUserIndex(gameId, socket.id);

        io.to(gameId).emit("gestureReceive", {
          type: "Disbelief",
          playerNo: users[socket.id].playerNo,
        });

        commonFold(gameId, currentUserIndex);
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("pokerAllInSend", async function (data) {
      try {
        let gameId = users[socket.id].gameId;
        // socket.to(gameId).emit('pokerFoldReceive', data)

        let currentUserIndex = await getUserIndex(gameId, socket.id);
        let nextUserIndex = await getNextUserIndex(gameId, currentUserIndex);

        // Set Current Player Value
        gameDetails[gameId].players[currentUserIndex].turn = false;
        gameDetails[gameId].players[currentUserIndex].allIn = true;
        gameDetails[gameId].players[currentUserIndex].lastTurn = "allIn";
        gameDetails[gameId].players[currentUserIndex].callValue =
          data.callValue;
        gameDetails[gameId].players[currentUserIndex].raiseValue = 0;

        // Update Bet Amount
        await updateBetAmount(gameId, currentUserIndex, data.callValue);

        // Set Next Player Value
        gameDetails[gameId].players[nextUserIndex].turn = true;
        if (
          gameDetails[gameId].players[nextUserIndex].callValue >= data.callValue
        ) {
          gameDetails[gameId].players[nextUserIndex].nextTurn = "check";
          gameDetails[gameId].checkUserId =
            gameDetails[gameId].players[nextUserIndex].user_id;
        } else {
          gameDetails[gameId].players[nextUserIndex].nextTurn = "call";
        }

        // Set Game Value
        gameDetails[gameId].potValue = await getPotValue(gameId);
        gameDetails[gameId].timer = 60;

        // Get Multiple Pot
        await getMultiplePotValue(gameId);

        // Send Game Status

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

        gameDetailsModel.update(
          { game_data: gameDetails[gameId] },
          {
            where: { id: gameDetails[gameId].currentTurnId },
          }
        );
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("disconnect", async function () {
      console.log("user " + socket.id + " disconnected");

      let userDetail = users[socket.id];
      delete users[socket.id];
      console.log("userDetail", userDetail);

      if (!userDetail) return;
      if (!gameDetails[userDetail.gameId]) return;

      let gameId = userDetail.gameId;

      let currentUserIndex = await getUserIndex(gameId, socket.id);
      // let playerNo = gameDetails[userDetail.gameId].players[currentUserIndex].playerNo;

      if (gameDetails[gameId].players && !gameDetails[gameId].gameStarted) {
        gameDetails[gameId].players = gameDetails[gameId].players.filter(
          (item) => item.socketId !== socket.id
        );
      } else {
        gameDetails[gameId].players.forEach((item, index) => {
          if (item.socketId == socket.id) {
            gameDetails[gameId].players[index].disconnected = true;
          }
        });
      }

      if (!gameDetails[gameId].gameStarted) {
        await setUserTurn(gameId);
      }

      // Send Game Status
      io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

      if (peerIds[gameId]) {
        let gameId = userDetail.gameId;

        console.log("gameDetails[gameId].players", gameDetails[gameId].players);
        console.log(
          "peerIds[gameId].players[currentUserIndex]",
          peerIds[gameId].players[currentUserIndex]
        );

        peerIds[gameId].players.forEach((item, index) => {
          if (item.socketId == socket.id) {
            peerIds[gameId].players.splice(index, 1);
          }
        });

        io.to(gameId).emit("peerUserList", peerIds[gameId]);
      }

      // Remove User From Game
      let count = 0;
      let alreadyQuit = false;
      if (userDetail.playerNo) {
        removeUserCount[socket.id] = setInterval(function () {
          count++;

          let alreadyJoined = false;

          gameDetails[gameId].players.forEach((item, index) => {
            if (item.user_id == userDetail.userId) {
              alreadyJoined = true;
            }
          });

          // console.log('removeUserCount', count)
          // console.log('alreadyJoined', alreadyJoined)
          // console.log('alreadyQuit', alreadyQuit)
          // console.log('userDetail', userDetail)

          if (alreadyJoined || alreadyQuit) {
            clearInterval(removeUserCount[socket.id]);
          }

          if (count >= 10 && !alreadyJoined) {
            if (!gameDetails[gameId].gameStarted) {
              alreadyQuit = true;

              gameUserQuit(userDetail.id, userDetail.userId);
              clearInterval(removeUserCount[socket.id]);
            }
          }
        }, 1000);
      }
    });

    // Sitting Out
    socket.on("sittingOutSet", async function (data) {
      try {
        // console.log('sittingOutSet', socket.id);

        let gameId = users[socket.id].gameId;
        // socket.to(gameId).emit('pokerCallReceive', data)

        let currentUserIndex = await getUserIndex(gameId, socket.id);

        gameDetails[gameId].players[currentUserIndex].sittingOut =
          data.sittingOut;

        // console.log('gameDetails[gameId].players[currentUserIndex]', gameDetails[gameId].players[currentUserIndex])

        if (!gameDetails[gameId].gameStarted) {
          await setUserTurn(gameId);
        }

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
      } catch (err) {
        console.log(err);
      }
    });

    // TimedOut Popup
    socket.on("timedOutPopupSet", async function (data) {
      try {
        let gameId = users[socket.id].gameId;
        let currentUserIndex = await getUserIndex(gameId, socket.id);
        let playerDetail = gameDetails[gameId].players[currentUserIndex];
        let sittingOutVal = false;
        let sittingOutInt = 0;

        if (data.leaveTable) {
          sittingOutVal = true;
          sittingOutInt = 1;
        }

        gameDetails[gameId].players[currentUserIndex].sittingOut =
          sittingOutVal;
        gameUserModel.update(
          { sitting_out: sittingOutInt },
          {
            where: {
              user_id: playerDetail.user_id,
              game_id: gameDetails[gameId].id,
              status: 1,
            },
          }
        );

        if (!gameDetails[gameId].gameStarted) {
          await setUserTurn(gameId);
        }

        gameDetails[gameId].players[currentUserIndex].timedOut = false;
        gameDetails[gameId].players[currentUserIndex].timedOutCount = 0;

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("reloadPageSend", async function (data) {
      console.log("reloadPageSend", socket.id);
      io.to(`user_${data.userId}`).emit("reloadPage", true);
    });

    // Pocker Peer Id Send
    socket.on("pokerPeerIdSend", async function (data) {
      try {
        let gameId = users[socket.id].gameId;
        // socket.to(gameId).emit('pokerPeerIdReceive', data)

        let currentUserIndex = await getUserIndex(gameId, socket.id);
        if (currentUserIndex >= 0) {
          let playerNo = gameDetails[gameId].players[currentUserIndex].playerNo;
          let playerIndex = null;

          gameDetails[gameId].players[currentUserIndex].peerId = data.peerId;

          if (!peerIds[gameId]) {
            peerIds[gameId] = { players: [] };
          }

          peerIds[gameId].players.forEach((item, index) => {
            if (playerNo == item.playerNo) {
              playerIndex = index;
            }
          });

          if (playerIndex === null) {
            peerIds[gameId].players.push({
              playerNo: playerNo,
              peerId: data.peerId,
              socketId: socket.id,
            });
          } else {
            peerIds[gameId].players[playerIndex] = {
              playerNo: playerNo,
              peerId: data.peerId,
              socketId: socket.id,
            };
          }
        }

        io.to(gameId).emit("peerUserList", peerIds[gameId]);
        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
      } catch (err) {
        console.log(err);
      }
    });

    // Chat Message
    socket.on("sendChatMessage", async function (data) {
      // console.log('sendChatMessage', data);
      io.to(data.gameId).emit("receiveChatMessage", data);

      gameChatsModel
        .create({
          game_id: data.game_id,
          game_user_id: data.game_user_id,
          from_id: data.from_id,
          from_type: data.from_type,
          from_player_no: data.from_player_no,
          chat_type: data.chat_type,
          message: data.message ? data.message : null,
          emoji_id: data.emoji_id ? data.emoji_id : null,
        })
        .then((result) => {})
        .catch((err) => {
          console.log(err);
        });
    });

    // Toogle Cam
    socket.on("webcamToggleSend", async function (data) {
      try {
        console.log("webcamToggleSend", socket.id);

        let gameId = users[socket.id].gameId;
        let currentUserIndex = await getUserIndex(gameId, socket.id);

        gameDetails[gameId].players[currentUserIndex].showCam = data.showCam;
        gameDetails[gameId].players[currentUserIndex].stream_id =
          data.stream_id;

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("getCamDetails", {
          playerNo: gameDetails[gameId].players[currentUserIndex].playerNo,
          publisherSocketId: socket.id,
        });
      } catch (err) {
        console.log(err);
      }
    });

    socket.on("peerIdSend", async function (data) {
      try {
        console.log("peerIdSend", data);
        console.log("socket.id", socket.id);

        // setTimeout(() => {
        //     io.to(data.publisherSocketId).emit('peerIdReceive', data)
        // }, 2000)
        io.to(data.publisherSocketId).emit("peerIdReceived", data);
      } catch (err) {
        console.log(err);
      }
    });

    // DEALER
    // ===============================
    // Reset Game
    socket.on("resetGameSend", async function (data) {
      console.log("resetGameSend", socket.id);

      try {
        let gameId = users[socket.id].gameId;

        gameDetails[gameId].dealerReset = true;

        console.log(
          "gameDetails[gameId].sbPlayerNo => 0",
          gameDetails[gameId].sbPlayerNo
        );

        if (gameDetails[gameId].sbPlayerNo) {
          if (gameDetails[gameId].sbPlayerNo < 2) {
            gameDetails[gameId].sbPlayerNo = null;
          } else {
            // gameDetails[gameId].sbPlayerNo = gameDetails[gameId].sbPlayerNo - 1

            let sbIndex = null;
            for (let i = gameDetails[gameId].players.length - 1; i >= 0; i--) {
              if (sbIndex && gameDetails[gameId].players[i]) {
                gameDetails[gameId].sbPlayerNo =
                  gameDetails[gameId].players[i].playerNo;
                break;
              } else {
                if (
                  gameDetails[gameId].players[i].playerNo ==
                  gameDetails[gameId].sbPlayerNo
                ) {
                  sbIndex = i;
                }
              }
            }
          }
        } else {
          gameDetails[gameId].sbPlayerNo = null;
        }

        console.log(
          "gameDetails[gameId].sbPlayerNo => 1",
          gameDetails[gameId].sbPlayerNo
        );

        for (let i = 0; i < gameDetails[gameId].players.length; i++) {
          if (gameDetails[gameId].players[i].turn) {
            gameDetails[gameId].players[i].turn = false;
          }

          gameDetails[gameId].players[i].turnStatus = null;
        }

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
        io.to(gameId).emit("dealerGameReset", gameDetails[gameId]);

        for (let i = 0; i < gameDetails[gameId].players.length; i++) {
          if (!gameDetails[gameId].players[i].sittingOut) {
            await updateBetAmount(gameId, i, 0);
          }
        }

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

        setTimeout(() => {
          resetGame(gameId);
        }, 3000);
      } catch (err) {
        console.log(err);
      }
    });
  });

  let dealHandCards = async (gameId, nextUserIndex) => {
    mqttData = {};
    gameDetails[gameId].cardMessage = "Waiting for dealer to send cards...";
    let gamePlayers = gameDetails[gameId].players;
    let count = 0;
    let interval = setInterval(function () {
      let totalPlayer = 0;
      let hasCards = 0;

      gamePlayers.forEach((player, index) => {
        if (!player.fold && !player.sittingOut) {
          totalPlayer++;
        }
        if (player.myCards.length == 2 && !player.sittingOut) {
          hasCards++;
        }
      });

      distributeCards(gameId);

      if (
        totalPlayer == hasCards ||
        gameDetails[gameId].winnerDeclared ||
        !gameDetails[gameId].gameStarted ||
        count > 900
      ) {
        clearInterval(interval);

        gameDetails[gameId].players[nextUserIndex].nextTurn = "call";
        gameDetails[gameId].players[nextUserIndex].turn = true;
        gameDetails[gameId].timer = 60;
        gameDetails[gameId].cardMessage = "";
        io.to(gameId).emit("pokerAudio", "deal_each_card_and_fold_cards");

        gameDetailsModel.update(
          { game_data: gameDetails[gameId] },
          {
            where: { id: gameDetails[gameId].currentTurnId },
          }
        );
      }

      count++;
      io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
    }, 1000);
  };

  // Common Check
  let commonCheck = async (gameId, currentUserIndex) => {
    console.log("commonCheck", gameId, currentUserIndex);

    let nextUserIndex = await getNextUserIndex(gameId, currentUserIndex);

    // Set Current Player Value
    gameDetails[gameId].players[currentUserIndex].raiseValue = 0;
    gameDetails[gameId].players[currentUserIndex].turn = false;
    gameDetails[gameId].players[currentUserIndex].lastTurn = "check";
    gameDetails[gameId].players[currentUserIndex].turnStatus = "check";

    // Set Next Player Value
    gameDetails[gameId].players[nextUserIndex].nextTurn = "check";

    // Set Game Value
    gameDetails[gameId].timer = 60;

    if (
      !gameDetails[gameId].firstCheck &&
      gameDetails[gameId].players[currentUserIndex].bigBlind
    ) {
      gameDetails[gameId].checkUserId =
        gameDetails[gameId].players[nextUserIndex].user_id;
      gameDetails[gameId].firstCheck = true;
    }

    if (
      gameDetails[gameId].checkUserId ==
      gameDetails[gameId].players[nextUserIndex].user_id
    ) {
      commonFetchCards(gameId, nextUserIndex);
    } else {
      gameDetails[gameId].players[nextUserIndex].turn = true;
    }

    // Send Game Status

    io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

    io.to(gameId).emit("gestureReceive", {
      type: "Check",
      playerNo: gameDetails[gameId].players[currentUserIndex].playerNo,
    });

    gameDetailsModel.update(
      { game_data: gameDetails[gameId] },
      {
        where: { id: gameDetails[gameId].currentTurnId },
      }
    );
  };

  // Common Fetch Cards
  let commonFetchCards = async (gameId, nextUserIndex) => {
    if (gameDetails[gameId].tableStep == "river") {
      let winnerId = await getWinnerDetails(gameId);
      let winnerIndex = null;

      await getMultiplePotValue(gameId);

      gameDetails[gameId].players[nextUserIndex].turn = false;

      await gameDetails[gameId].players.forEach((player, index) => {
        if (player.user_id == winnerId) {
          gameDetails[gameId].winnerDeclared = true;
          gameDetails[gameId].players[index].winner = true;
          gameDetails[gameId].players[index].turnStatus = "winner";
          winnerIndex = index;

          io.to(gameId).emit("gestureReceive", {
            type: "Check",
            playerNo: gameDetails[gameId].players[index].playerNo,
          });
        } else {
          gameDetails[gameId].players[index].turnStatus = "";

          io.to(gameId).emit("gestureReceive", {
            type: "Disbelief",
            playerNo: gameDetails[gameId].players[index].playerNo,
          });
        }
      });

      // sendToWinner(gameId, winnerIndex);
      // sendAmountToWinner(gameId);

      await sendPotAmount(gameId);

      gameDetailsModel.update(
        {
          winner_id: winnerId,
          game_data: gameDetails[gameId],
          status: 2,
        },
        {
          where: { id: gameDetails[gameId].currentTurnId },
        }
      );

      io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

      setTimeout(() => {
        io.to(gameId).emit("pokerAudio", "winner");
        io.to(gameId).emit("pokerAudio", "not-winner-other_players");
      }, 1000);

      setTimeout(() => {
        resetGame(gameId);
      }, 10000);
    } else {
      let count = 0;

      // Flop Cards
      if (gameDetails[gameId].tableStep == null) {
        gameDetails[gameId].tableStep = "flop";

        gameDetails[gameId].cardMessage = "Waiting for flop cards.....";

        let interval = setInterval(async function () {
          drawTableCards(gameId);

          if (
            gameDetails[gameId].serveCards.length > 2 ||
            gameDetails[gameId].winnerDeclared ||
            !gameDetails[gameId].gameStarted ||
            count > 900
          ) {
            clearInterval(interval);

            await getMultiplePotValue(gameId);

            gameDetails[gameId].timer = 60;
            gameDetails[gameId].players[nextUserIndex].turn = true;
            gameDetails[gameId].cardMessage = "";

            resetTurnStatus(gameId);

            io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
            io.to(gameId).emit("pokerAudio", "deal_each_card_and_fold_cards");

            gameDetailsModel.update(
              { game_data: gameDetails[gameId] },
              {
                where: { id: gameDetails[gameId].currentTurnId },
              }
            );
          }

          count++;
        }, 1000);
      }

      // Turn Cards
      else if (gameDetails[gameId].tableStep == "flop") {
        gameDetails[gameId].tableStep = "turn";
        gameDetails[gameId].cardMessage = "Waiting for turn cards.....";

        let interval = setInterval(async function () {
          drawTableCards(gameId);

          if (
            gameDetails[gameId].serveCards.length > 3 ||
            gameDetails[gameId].winnerDeclared ||
            !gameDetails[gameId].gameStarted ||
            count > 900
          ) {
            clearInterval(interval);

            await getMultiplePotValue(gameId);

            gameDetails[gameId].timer = 60;
            gameDetails[gameId].players[nextUserIndex].turn = true;
            gameDetails[gameId].cardMessage = "";

            resetTurnStatus(gameId);

            io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
            io.to(gameId).emit("pokerAudio", "deal_each_card_and_fold_cards");

            gameDetailsModel.update(
              { game_data: gameDetails[gameId] },
              {
                where: { id: gameDetails[gameId].currentTurnId },
              }
            );
          }

          count++;
        }, 1000);
      }

      // River Cards
      else if (gameDetails[gameId].tableStep == "turn") {
        gameDetails[gameId].tableStep = "river";
        gameDetails[gameId].cardMessage = "Waiting for river cards.....";

        let interval = setInterval(async function () {
          drawTableCards(gameId);

          if (
            gameDetails[gameId].serveCards.length > 4 ||
            gameDetails[gameId].winnerDeclared ||
            !gameDetails[gameId].gameStarted ||
            count > 900
          ) {
            clearInterval(interval);

            await getMultiplePotValue(gameId);

            gameDetails[gameId].timer = 60;
            gameDetails[gameId].players[nextUserIndex].turn = true;
            gameDetails[gameId].cardMessage = "";

            resetTurnStatus(gameId);

            io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
            io.to(gameId).emit("pokerAudio", "deal_each_card_and_fold_cards");

            gameDetailsModel.update(
              { game_data: gameDetails[gameId] },
              {
                where: { id: gameDetails[gameId].currentTurnId },
              }
            );
          }

          count++;
        }, 1000);
      } else {
        gameDetails[gameId].players[nextUserIndex].turn = true;
      }
    }

    let resetTurnStatus = (gameId) => {
      gameDetails[gameId].players.forEach((player) => {
        player.turnStatus = "";
      });
    };
  };

  // Common Fold
  let commonFold = async (gameId, currentUserIndex) => {
    let nextUserIndex = await getNextUserIndex(gameId, currentUserIndex);

    gameDetails[gameId].players[currentUserIndex].turn = false;
    gameDetails[gameId].players[currentUserIndex].lastTurn = "fold";
    gameDetails[gameId].players[currentUserIndex].turnStatus = "fold";
    gameDetails[gameId].players[currentUserIndex].fold = true;

    // Winner Check
    let winnerIndex = await getWinnerIndex(gameId);
    if (winnerIndex < 0) {
      gameDetails[gameId].players[nextUserIndex].turn = true;
      gameDetails[gameId].players[nextUserIndex].nextTurn =
        gameDetails[gameId].players[currentUserIndex].nextTurn;

      if (
        gameDetails[gameId].checkUserId ==
        gameDetails[gameId].players[currentUserIndex].user_id
      ) {
        gameDetails[gameId].checkUserId =
          gameDetails[gameId].players[nextUserIndex].user_id;
      }
    } else {
      gameDetails[gameId].winnerDeclared = true;
      gameDetails[gameId].players[winnerIndex].winner = true;
      gameDetails[gameId].players[winnerIndex].turnStatus = "winner";

      gameDetailsModel.update(
        {
          winner_id: gameDetails[gameId].players[winnerIndex].user_id,
          game_data: gameDetails[gameId],
          status: 2,
        },
        {
          where: { id: gameDetails[gameId].currentTurnId },
        }
      );

      sendToWinner(gameId, winnerIndex);

      setTimeout(() => {
        io.to(gameId).emit("pokerAudio", "winner");
        io.to(gameId).emit("pokerAudio", "not-winner-other_players");
      }, 1000);

      setTimeout(() => {
        resetGame(gameId);
      }, 10000);
    }

    await getMultiplePotValue(gameId);

    gameDetails[gameId].timer = 60;

    io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
    io.to(gameId).emit("pokerAudio", "deal_each_card_and_fold_cards");
  };

  let getPotValue = async (gameId) => {
    let value = 0;

    gameDetails[gameId].players.forEach((item) => {
      value += item.callValue;
    });

    return value;
  };

  // ========================================
  // Multiple Pot Start
  // ========================================
  let getMultiplePotValue = async (gameId) => {
    let value = 0;
    let potCount = 1;
    let userList = [];
    let allInList = [];
    let tablePlayers = gameDetails[gameId].players;

    tablePlayers.forEach((item, index) => {
      value += item.callValue;

      if (!item.fold && !item.sittingOut) {
        if (item.allIn) {
          allInList.push({ index: index, value: item.callValue * 1 });
          potCount++;
        } else {
          userList.push(index);
        }
      }
    });

    gameDetails[gameId].potList = [];

    let totalPotVal = 0;
    if (potCount > 1) {
      allInList.sort(function (a, b) {
        return a.value - b.value;
      });

      allInList.forEach((item, index) => {
        let curPotVal = 0;
        let allUserList = [];

        tablePlayers.forEach((player, playerIndex) => {
          if (player.callValue >= item.value) {
            curPotVal += item.value * 1;

            if (!player.fold && !player.sittingOut) {
              allUserList.push(playerIndex);
            }
          } else {
            curPotVal += player.callValue * 1;
          }
        });

        let newPotVal = curPotVal - totalPotVal;
        totalPotVal = curPotVal;

        gameDetails[gameId].potList.push({
          value: newPotVal,
          userList: allUserList,
          winnerIndex: null,
        });
      });
    }

    // Genrate Pot List
    if (value - totalPotVal > 0) {
      gameDetails[gameId].potList.push({
        value: value - totalPotVal,
        userList: userList,
        winnerIndex: null,
      });
    }

    if (gameDetails[gameId].serveCards.length) {
      for (let i = 0; i < gameDetails[gameId].potList.length; i++) {
        let winner = await getPotWinner(gameId, i);
        if (gameDetails[gameId].players[winner]) {
          gameDetails[gameId].potList[i].winnerIndex = winner;
          gameDetails[gameId].potList[i].winnerId =
            gameDetails[gameId].players[winner].user_id;
          gameDetails[gameId].potList[i].playerNo =
            gameDetails[gameId].players[winner].playerNo;
        }
      }
    }

    return;
  };

  let getPotWinner = async (gameId, potindex) => {
    let winnerIndex = null;
    let serveCards = gameDetails[gameId].serveCards.map((card) => {
      return card.code;
    });

    let players = [];
    gameDetails[gameId].potList[potindex].userList.forEach((item) => {
      players.push(gameDetails[gameId].players[item]);
    });

    let handStatusList = [];
    await players.forEach(async (user, index) => {
      let cards = [];
      if (user.myCards) {
        cards = user.myCards.map((card) => {
          return card.code;
        });
      }
      cards = [...serveCards, ...cards];
      players[index].handStatus = await Hand.solve(cards);
      handStatusList.push(players[index].handStatus);
    });

    // for (let i = 0; i < players.length; i++) {
    //     let cards = []
    //     if (players[i].myCards) {
    //         cards = players[i].myCards.map((card) => {
    //             return card.code
    //         })
    //     }
    //     cards = [...serveCards, ...cards];
    //     players[i].handStatus = await Hand.solve(cards);
    //     handStatusList.push(players[i].handStatus)
    // }

    let winner = await Hand.winners(handStatusList);

    players.forEach((user, index) => {
      if (winner[0] == user.handStatus) {
        winnerIndex = gameDetails[gameId].potList[potindex].userList[index];
      }
    });

    return winnerIndex;
  };

  let sendPotAmount = async (gameId) => {
    let potList = gameDetails[gameId].potList;

    for (let i = 0; i < potList.length; i++) {
      let winningAmount = null;
      let wIndex = potList[i].winnerIndex;

      if (wIndex != null) {
        let potVal = gameDetails[gameId].potList[i].value * 1;
        let playerDetails = gameDetails[gameId].players[wIndex];
        let betAmount = playerDetails.betAmount * 1;
        winningAmount = betAmount + potVal;

        gameDetails[gameId].players[wIndex].betAmount = winningAmount;

        await gameUserModel.update(
          { bet_amount: winningAmount },
          {
            where: {
              user_id: playerDetails.user_id,
              game_id: gameDetails[gameId].id,
              status: 1,
            },
          }
        );
      }
    }

    return;
  };
  // ========================================
  // Multiple Pot End
  // ========================================

  let updateBetAmount = async (gameId, userIndex, amount) => {
    console.log("updateBetAmount", gameId, userIndex, amount);

    if (!gameDetails[gameId]) {
      return;
    }
    if (!gameDetails[gameId].players) {
      return;
    }
    if (!gameDetails[gameId].players[userIndex]) {
      return;
    }

    if (gameDetails[gameId].players[userIndex].betAmount > 0) {
      gameDetails[gameId].players[userIndex].betAmount =
        gameDetails[gameId].players[userIndex].beginingAmount - amount;

      let userDetails = gameDetails[gameId].players[userIndex];

      // console.log('gameDetails[gameId].players[userIndex].betAmount', userDetails.betAmount)
      // console.log('gameDetails[gameId].players[userIndex].user_id', userDetails.user_id)
      // console.log('gameDetails[gameId].id', gameDetails[gameId].id)

      gameUserModel.update(
        { bet_amount: userDetails.betAmount },
        {
          where: {
            user_id: userDetails.user_id,
            game_id: gameDetails[gameId].id,
            status: 1,
          },
        }
      );
    }
  };

  let getUserIndex = async (gameId, socketId) => {
    if (gameDetails[gameId]) {
      return gameDetails[gameId].players.findIndex(
        (item) => item.socketId === socketId
      );
    } else {
      return -1;
    }
  };

  let setUserTimeout = async (gameId) => {
    let currentUserIndex = -1;

    gameDetails[gameId].players.forEach((item, index) => {
      if (item.turn) {
        currentUserIndex = index;
      }
    });

    if (currentUserIndex > -1) {
      let nextTurn = gameDetails[gameId].players[currentUserIndex].nextTurn;

      if (nextTurn == "check") {
        commonCheck(gameId, currentUserIndex);
      } else if (nextTurn == "straddle") {
        gameDetails[gameId].players[currentUserIndex].turn = false;
        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);

        dealHandCards(gameId, currentUserIndex);
      } else {
        commonFold(gameId, currentUserIndex);

        // Timed Out Popup
        gameDetails[gameId].players[currentUserIndex].timedOut = true;
        gameDetails[gameId].players[currentUserIndex].timedOutCount = 60;

        let timeoutCount = setInterval(function () {
          if (!gameDetails[gameId].players[currentUserIndex]) {
            clearInterval(timeoutCount);
            return;
          }

          if (!gameDetails[gameId].players[currentUserIndex].timedOut) {
            clearInterval(timeoutCount);
            return;
          }

          if (!gameDetails[gameId].gameStarted) {
            timedOutSitOut(gameId, currentUserIndex);
          }

          if (gameDetails[gameId].timedOutCount <= 0) {
            if (gameDetails[gameId].players[currentUserIndex].timedOut) {
              timedOutSitOut(gameId, currentUserIndex);
            }

            clearInterval(timeoutCount);
            gameDetails[gameId].players[currentUserIndex].timedOut = false;
            gameDetails[gameId].players[currentUserIndex].timedOutCount = 0;

            io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
          } else {
            gameDetails[gameId].players[currentUserIndex].timedOutCount--;
          }
        }, 1000);

        io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
      }
    }
  };

  let timedOutSitOut = async (gameId, currentUserIndex) => {
    if (gameDetails[gameId].players[currentUserIndex]) {
      let playerDetail = gameDetails[gameId].players[currentUserIndex];
      gameDetails[gameId].players[currentUserIndex].sittingOut = true;
      gameUserModel.update(
        { sitting_out: 1 },
        {
          where: {
            user_id: playerDetail.user_id,
            game_id: gameDetails[gameId].id,
            status: 1,
          },
        }
      );

      io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
    }
  };

  let getNextUserIndex = async (gameId, currentUserIndex) => {
    let index = 0;

    if (gameDetails[gameId].players.length == currentUserIndex + 1) {
      index = 0;
    } else {
      index = currentUserIndex + 1;
    }

    if (
      gameDetails[gameId].players[index].fold ||
      gameDetails[gameId].players[index].sittingOut
    ) {
      return getNextUserIndex(gameId, index);
    } else {
      return index;
    }
  };

  let getWinnerIndex = async (gameId) => {
    let winnerIndex = -1;

    let playerNotFold = gameDetails[gameId].players.filter((item) => {
      if (!item.fold && !item.sittingOut) {
        return item;
      }
    });
    if (playerNotFold.length == 1) {
      gameDetails[gameId].players.forEach((item, index) => {
        if (!item.fold && !item.sittingOut) {
          winnerIndex = index;
        }
      });
    }

    return winnerIndex;
  };

  let sendToWinner = async (gameId, userIndex) => {
    console.log("sendToWinner");
    if (!gameDetails[gameId]) {
      return;
    }
    if (!gameDetails[gameId].players) {
      return;
    }
    if (!gameDetails[gameId].players[userIndex]) {
      return;
    }

    if (!gameDetails[gameId].players[userIndex].betAmount) {
      gameDetails[gameId].players[userIndex].betAmount = 0;
    }

    gameDetails[gameId].players[userIndex].betAmount +=
      gameDetails[gameId].potValue;

    let userDetails = gameDetails[gameId].players[userIndex];

    gameUserModel.update(
      { bet_amount: gameDetails[gameId].players[userIndex].betAmount },
      {
        where: {
          user_id: userDetails.user_id,
          game_id: gameDetails[gameId].id,
          status: 1,
        },
      }
    );
  };

  let sendAmountToWinner = async (gameId) => {
    let winnerIndex = null;
    let winningAmount = 0;

    let allInWinner = false;
    let winnerCallValue = 0;
    let remainingPlayers = [];

    await gameDetails[gameId].players.forEach((item, index) => {
      if (item.winner) {
        winnerIndex = index;
        if (item.allIn) {
          allInWinner = true;
          winnerCallValue = item.callValue * 1;
        } else {
          winningAmount = item.betAmount + gameDetails[gameId].potValue;
        }
      } else {
        if (!item.fold && !item.sittingOut && item.handStatus) {
          let userRank = {
            index: index,
            rank: 0,
            subRank: 0,
          };

          if (item.handStatus.rank) {
            userRank.rank = item.handStatus.rank;
          }

          if (item.handStatus.cards) {
            item.handStatus.cards.forEach((card) => {
              if (card.rank) {
                userRank.subRank += card.rank;
              }
            });
          }

          remainingPlayers.push(userRank);
        }
      }
    });

    // First Slot
    if (allInWinner) {
      winningAmount = 0;

      gameDetails[gameId].players.forEach((player) => {
        let userAmount = player.callValue * 1;

        if (winnerCallValue >= userAmount) {
          winningAmount += userAmount;
        } else {
          winningAmount += winnerCallValue;
        }
      });
    }

    gameDetails[gameId].players[winnerIndex].betAmount = winningAmount;

    if (winnerIndex != null) {
      gameUserModel.update(
        { bet_amount: winningAmount },
        {
          where: {
            user_id: gameDetails[gameId].players[winnerIndex].user_id,
            game_id: gameDetails[gameId].id,
            status: 1,
          },
        }
      );
    }

    // Second Slot
    let remainingAmount = gameDetails[gameId].potValue - winningAmount;
    let secondWinnerIndex = null;
    let mainRank = 0;
    let subRank = 0;

    if (allInWinner && remainingAmount > 0) {
      remainingPlayers.forEach((item) => {
        if (item.rank > mainRank) {
          mainRank = item.rank;
          subRank = item.subRank;
          secondWinnerIndex = item.index;

          console.log("item.rank > mainRank", item);
        } else if (item.rank == mainRank && item.subRank > subRank) {
          subRank = item.subRank;
          secondWinnerIndex = item.index;

          console.log("item.rank == mainRank", item);
        }
      });
    }

    if (secondWinnerIndex != null) {
      gameDetails[gameId].players[secondWinnerIndex].betAmount +=
        remainingAmount;
      gameUserModel.update(
        { bet_amount: remainingAmount },
        {
          where: {
            user_id: gameDetails[gameId].players[secondWinnerIndex].user_id,
            game_id: gameDetails[gameId].id,
            status: 1,
          },
        }
      );
    }

    io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
  };

  let resetGame = async (gameId) => {
    mqttData = {};
    tableCards = [];

    gameDetails[gameId].serveCards = [];
    gameDetails[gameId].timer = 60;
    gameDetails[gameId].currentTurnId = null;
    gameDetails[gameId].potValue = 0;
    gameDetails[gameId].gameStarted = false;
    gameDetails[gameId].winnerDeclared = false;
    gameDetails[gameId].checkUserId = null;
    gameDetails[gameId].callValue = 0;
    gameDetails[gameId].tableStep = null;
    gameDetails[gameId].winningDetails = null;
    gameDetails[gameId].potRaised = false;
    gameDetails[gameId].potList = [];

    gameDetails[gameId].players.forEach((item) => {
      item.myCards = [];
      item.callValue = 0;
      item.increasedCallValue = 0;
      item.raiseValue = 0;
      item.beginingAmount = item.betAmount;

      item.handStatus = null;
      item.nextTurn = "blind";
      item.lastTurn = "";
      item.turnStatus = "";

      item.turn = false;
      item.fold = false;
      item.winner = false;
      item.smallBlind = false;
      item.bigBlind = false;
      item.allIn = false;
      item.dealer = false;
    });

    gameDetails[gameId].players = gameDetails[gameId].players.filter((item) => {
      if (!item.disconnected) {
        return item;
      }
    });

    await setUserTurn(gameId);

    io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
  };

  let setUserTurnOld = async (gameId) => {
    if (!gameDetails[gameId]) {
      return;
    }

    if (gameDetails[gameId].sbPlayerNo) {
      let playerIndex = null;
      let turnIndex = null;

      playerIndex = gameDetails[gameId].sbPlayerNo - 1;

      // console.log('playerIndex', playerIndex)

      if (gameDetails[gameId].players.length < 1) {
        return;
      }

      if (playerIndex == null) {
        let setTurn = false;
        gameDetails[gameId].players.forEach((item, index) => {
          if (!item.sittingOut && !setTurn) {
            gameDetails[gameId].players[index].turn = true;
            setTurn = true;
          }
        });
        return;
      }

      if (playerIndex == gameDetails[gameId].players.length - 1) {
        turnIndex = 0;
      } else {
        turnIndex = playerIndex + 1;
      }

      console.log("turnIndex", turnIndex);

      if (turnIndex != null && gameDetails[gameId].players[turnIndex]) {
        gameDetails[gameId].players[turnIndex].turn = true;
      } else {
        gameDetails[gameId].players[0].turn = true;
      }
    } else {
      gameDetails[gameId].players[0].turn = true;
    }

    return gameId;
  };

  let setUserTurn = async (gameId) => {
    if (!gameDetails[gameId]) {
      return;
    }

    let playerIndex = null;
    let turnIndex = null;
    let joinCount = 0;

    gameDetails[gameId].players.forEach((item, index) => {
      if (gameDetails[gameId].sbPlayerNo != null) {
        if (item.playerNo == gameDetails[gameId].sbPlayerNo) {
          playerIndex = index;
        }
      }

      if (!item.sittingOut) {
        joinCount++;
      }

      gameDetails[gameId].players[index].turn = false;
    });

    if (joinCount > 1) {
      turnIndex = await nextTurnIndex(gameId, playerIndex);

      if (turnIndex != null && gameDetails[gameId].players[turnIndex]) {
        gameDetails[gameId].cardMessage = null;
        gameDetails[gameId].players[turnIndex].turn = true;
      }
    } else {
      gameDetails[gameId].cardMessage = "Waiting for more players.....";
    }

    gameDetails[gameId].noPlayerArr = [];
    if (
      joinCount > 1 &&
      !gameDetails[gameId].dealerReset &&
      playerIndex != null
    ) {
      if (
        gameDetails[gameId].players[playerIndex] &&
        gameDetails[gameId].players[turnIndex]
      ) {
        let curPlayerNo = gameDetails[gameId].players[playerIndex].playerNo;
        let nextPlayerNo = gameDetails[gameId].players[turnIndex].playerNo;
        let noPlayerArr = [];

        //
        if (nextPlayerNo > curPlayerNo) {
          for (let i = curPlayerNo + 1; i < nextPlayerNo; i++) {
            noPlayerArr.push(i);
          }
        } else if (nextPlayerNo < curPlayerNo) {
          if (curPlayerNo < 9) {
            for (let i = curPlayerNo + 1; i < 10; i++) {
              noPlayerArr.push(i);
            }
          }

          if (nextPlayerNo > 0) {
            for (let i = 1; i < nextPlayerNo; i++) {
              noPlayerArr.push(i);
            }
          }
        }

        gameDetails[gameId].noPlayerArr = noPlayerArr;
      }
    }

    return gameId;
  };

  let nextTurnIndex = async (gameId, index, startIndex) => {
    if (!gameDetails[gameId]) {
      return;
    }
    let nextIndex = null;

    // console.log('nextTurnIndex', gameId, index, startIndex)

    if (index != null) {
      nextIndex = index + 1;
      if (!gameDetails[gameId].players[nextIndex]) {
        nextIndex = 0;
      }
    } else {
      nextIndex = 0;
    }

    // console.log('nextIndex', nextIndex)

    if (gameDetails[gameId].players[nextIndex].sittingOut) {
      // console.log('nextTurnIndex - sittingOut', gameId, nextIndex)
      if (nextIndex == startIndex) {
        return null;
      }

      return nextTurnIndex(gameId, nextIndex, index);
    } else {
      // console.log('nextTurnIndex - nextIndex', gameId, nextIndex)
      return nextIndex;
    }
  };

  let distributeCards = async (gameId) => {
    // mqttData[gameId];
    console.log("distributeCards", gameId);

    if (!gameDetails[gameId].players) {
      return;
    }

    if (!gameDetails[gameId].players[0]) {
      return;
    }

    gameDetails[gameId].players.forEach((item, index) => {
      let cards = [];

      // if (!mqttData[item.playerNo]) {
      //     mqttData[item.playerNo] = demoMqttData[item.playerNo]
      // }

      // if (mqttData[item.playerNo].length < 2) {
      //     console.log("if (mqttData[item.playerNo].length < 2) {")
      //     demoMqttData[item.playerNo].forEach((card) => {
      //         if (!mqttData[item.playerNo].includes(card) && mqttData[item.playerNo].length < 2) {
      //             console.log("mqttData[card.playerNo].length", card)
      //             mqttData[item.playerNo].push(card)
      //         }
      //     })
      // }

      if (gameDetails[gameId].tableGame) {
        if (mqttData[item.playerNo]) {
          cards = mqttData[item.playerNo];
        }
      } else {
        if (randomCardData[gameId]) {
          if (randomCardData[gameId][item.playerNo]) {
            cards = randomCardData[gameId][item.playerNo];
          }
        }
      }

      cards.forEach((card) => {
        io.emit("cardPlayerReceive", card);

        let cardData = null;
        cardValues.forEach((value) => {
          if (value.epc == card.EPC) {
            cardData = value;
          }
        });

        if (cardData && item.myCards.length < 2) {
          let hasCard = false;
          item.myCards.forEach((itemCard) => {
            if (itemCard.code == cardData.code) {
              hasCard = true;
            }
          });

          if (!hasCard) {
            item.myCards.push(cardData);
          }
        }
      });
    });
  };

  let drawTableCards = async (gameId) => {
    tableCards = [];

    let serveCards = [];
    let allServeCards = [];

    let allTableCards = [];
    let spliceCards = [];

    if (!gameId) {
      return;
    }
    if (!gameDetails[gameId]) {
      return;
    }

    // Serve Cards
    if (gameDetails[gameId].tableGame) {
      allServeCards = mqttData["11"];
    } else {
      if (!randomCardData[gameId]) {
        return;
      }
      if (!randomCardData[gameId]["11"]) {
        return;
      }

      allServeCards = randomCardData[gameId]["11"];
    }

    // Null Table Cards
    if (gameDetails[gameId].tableStep == null || !allServeCards) {
      return;
    }

    // Flop Cards
    if (gameDetails[gameId].tableStep == "flop") {
      // mqttData['11'] = allServeCards.splice(0, 3)

      allServeCards.forEach((card, i) => {
        if (i < 3) {
          spliceCards.push(card);
        }
      });
    }

    // Turn Cards
    if (gameDetails[gameId].tableStep == "turn") {
      // mqttData['11'] = allServeCards.splice(0, 4)

      allServeCards.forEach((card, i) => {
        if (i < 4) {
          spliceCards.push(card);
        }
      });
    }

    // River Cards
    if (gameDetails[gameId].tableStep == "river") {
      // mqttData['11'] = allServeCards.splice(0, 5);

      allServeCards.forEach((card, i) => {
        if (i < 5) {
          spliceCards.push(card);
        }
      });
    }

    // mqttData['11'].forEach((card, i) => {
    spliceCards.forEach((card, i) => {
      let hasCard = false;

      if (card) {
        allTableCards.forEach((item) => {
          // console.log("item.EPC", item.EPC)
          // console.log("card.EPC", card.EPC)

          if (item.EPC == card.EPC) {
            hasCard = true;
          }
        });

        if (!hasCard) {
          allTableCards.push(card);
          // io.emit('cardTableReceive', card)
        }
      }
    });

    if (gameDetails[gameId].tableGame) {
      tableCards = allTableCards;
    }

    allTableCards.forEach((item) => {
      let cardData = null;
      cardValues.forEach((value) => {
        if (value.epc == item.EPC) {
          cardData = value;
        }
      });

      serveCards.push(cardData);
    });

    gameDetails[gameId].serveCards = serveCards;

    io.to(gameId).emit("pokerGameDetails", gameDetails[gameId]);
  };

  let getWinnerDetails = async (gameId) => {
    let winnerId = 0;
    let serveCards = gameDetails[gameId].serveCards.map((card) => {
      return card.code;
    });
    let players = gameDetails[gameId].players.filter((item) => {
      if (!item.fold && !item.sittingOut) {
        return item;
      }
    });
    let handStatusList = [];

    // console.log("mqttData[gameId]", mqttData)

    await players.forEach(async (user, index) => {
      let cards = [];

      if (user.myCards) {
        cards = user.myCards.map((card) => {
          return card.code;
        });
      }

      cards = [...serveCards, ...cards];

      // console.log("cards", cards)

      players[index].handStatus = await Hand.solve(cards);
      // console.log(`handStatus - ${index}`, players[index].handStatus)
      handStatusList.push(players[index].handStatus);
    });

    // console.log("players", players)
    let winner = await Hand.winners(handStatusList);
    if (winner[0]) {
      gameDetails[gameId].winningDetails = winner[0];
    }

    // console.log("winner", winner)

    await players.forEach((user, index) => {
      if (winner[0] == user.handStatus) {
        // console.log("WinnerUser", user)
        winnerId = user.user_id;
      }
    });

    return winnerId;
  };

  // Game User Quit
  let gameUserQuit = (game_id, user_id) => {
    console.log("gameUserQuit", game_id, user_id);

    return new Promise(async (resolve, reject) => {
      try {
        const gameUser = await gameUserModel.findOne({
          where: {
            user_id: user_id,
            game_id: game_id,
            status: 1,
          },
        });

        const userResponse = await callApi("get", "/user", { id: user_id });
        if (!userResponse) {
          return resolve(false);
        }
        const user = userResponse.data;
        // Update User
        let updateGameUser = await gameUserModel.update(
          {
            withdraw_amount: gameUser.bet_amount,
            status: 2,
          },
          { where: { id: gameUser.id } }
        );

        // Not Update Game User
        if (!updateGameUser) {
          return;
        }
        // Adjust balance using PHP REST API
        const adjustResponse = await callApi("POST", "/adjust_cash_balance", {
          user_id: user_id,
          amount: gameUser.bet_amount,
          type: CASH_OPERATION.ADD,
          gateway: "game_quit",
        });

        if (!adjustResponse.status) {
          console.log("Failed to adjust balance");
          // rollback game user
          await gameUserModel.update(
            { withdraw_amount: null, status: 1 },
            { where: { id: gameUser.id } }
          );
          return resolve(false);
        }

        resolve(true);
      } catch (err) {
        resolve(false);
      }
    });
  };
};

// demoMqttData
// demoMqttData = {
//     '1': [
//         {
//             EPC: 'e20047084ba0642637aa0114',
//             TID: 'e280382120006426',
//             Mux1: '1',
//             'UTC time': '1704712684558'
//         },
//         {
//             EPC: 'e200470b4de0642667ce0109',
//             TID: 'e280382120006426',
//             Mux1: '1',
//             'UTC time': '1704712684558'
//         }
//     ],
//     '3': [
//         {
//             EPC: 'e200470cca8064267f98010d',
//             TID: 'e280382120006426',
//             Mux1: '3',
//             'UTC time': '1704712672516'
//         },
//         {
//             EPC: 'e2004712d2206426e012010c',
//             TID: 'e280382120006426',
//             Mux1: '3',
//             'UTC time': '1704712672516'
//         }
//     ],
//     '5': [
//         {
//             EPC: 'e200471097e06426bc6e010d',
//             TID: 'e280382120006426',
//             Mux1: '5',
//             'UTC time': '1704712690403'
//         },
//         {
//             EPC: 'e20047115ec06426c8dc0109',
//             TID: 'e280382120006426',
//             Mux1: '5',
//             'UTC time': '1704712690403'
//         }
//     ],
//     '7': [
//         {
//             EPC: 'e200470900f0642642ff0111',
//             TID: 'e280382120006426',
//             Mux1: '7',
//             'UTC time': '1704712688957'
//         },
//         {
//             EPC: 'e20047005df0682192770111',
//             TID: 'e280382120006821',
//             Mux1: '7',
//             'UTC time': '1704712688958'
//         }
//     ],
//     '9': [
//         {
//             EPC: 'e200470c1960642674860113',
//             TID: 'e280382120006426',
//             Mux1: '9',
//             'UTC time': '1704712678630'
//         },
//         {
//             EPC: 'e200470613f06426142f010d',
//             TID: 'e280382120006426',
//             Mux1: '9',
//             'UTC time': '1704712678630'
//         }
//     ],
//     '11': [
//         {
//             EPC: 'e2004707863064262b53010e',
//             TID: 'e280382120006426',
//             Mux1: '11',
//             'UTC time': '1704712668634'
//         },
//         {
//             EPC: 'e20047078b0064262ba0010d',
//             TID: 'e280382120006426',
//             Mux1: '11',
//             'UTC time': '1704712689787'
//         },
//         {
//             EPC: 'e200470a926064265c160111',
//             TID: 'e280382120006426',
//             Mux1: '11',
//             'UTC time': '1704712698550'
//         },
//         {
//             EPC: 'e200470a55f0682131f7010b',
//             TID: 'e280382120006821',
//             Mux1: '11',
//             'UTC time': '1704712698550'
//         },
//         {
//             EPC: 'e200470b575064266865010a',
//             TID: 'e280382120006426',
//             Mux1: '11',
//             'UTC time': '1704712698550'
//         }
//     ]
// }
