import { Component, ViewChild, OnInit, Inject, AfterViewInit, ElementRef } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeResourceUrl, } from '@angular/platform-browser';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Peer } from "peerjs";
import { ToastrService } from 'ngx-toastr';
import Swal from 'sweetalert2';

const Hand = require('pokersolver').Hand;

import { environment } from 'src/environments/environment';
import { rfidValues } from 'src/rfid_values';

import { AccountService } from '../../_services/account.service';
import { GameService } from '../../_services/game.service';
import { SocketService } from '../../_services/socket.service';

@Component({
  selector: 'app-table-view',
  templateUrl: './table-view.component.html',
  styleUrls: ['./table-view.component.css']
})

export class TableViewComponent {
  @ViewChild("me") me: any;
  @ViewChild("remote") remote: any;

  emojiUrl = environment.emojiUrl;
  feedUrl = environment.feedUrl;

  cardValues = rfidValues.cards;

  userDetails: any;
  profileDetails: any;
  gameDetails: any;
  token: any;
  cameraUrl: SafeResourceUrl | undefined;
  backgroundUrl: SafeResourceUrl | undefined;
  showCam: boolean = false;

  // Peer 
  // ===============================================
  peerConfig = {
    host: environment.peer_host,
    port: environment.peer_port,
    path: environment.peer_path,
    config: environment.peer_config,
    // key: 'oz9b3ni30qtcsor',
    // debug: 3
  }
  peer = new Peer("", this.peerConfig);

  callActive: boolean = false;
  pc: any;
  localStream: any;
  senderId = "";
  buyAmount: any = 100;
  buyPopup: boolean = false;

  showWebcam = false
  mySteam: any = new MediaStream();
  peerUserList: any = [];
  table_view_cam: any;
  table_view_cam_1: any;
  table_view_cam_2: any;
  table_view_cam_3: any;
  table_view_cam_4: any;
  table_view_cam_5: any;
  table_view_cam_6: any;
  table_view_cam_7: any;
  table_view_cam_8: any;
  table_view_cam_9: any;

  userJoined: any = [];

  // Poker Gameplay
  // ===============================================
  gameData: any = {};
  serveCards: any = [];
  myCards: any = [];
  cardMessage: any = "";

  myindex: any = 0;
  playerNo: any = 0;
  gameUser: any = {};
  socketId: any;
  betAmount: any = 0;
  beginingAmount: any = 0;
  maxAmount: any = 0;

  otherUserIndexes: any = [];
  countDownVal = 60;
  progressBarWidth = 100;
  userFold = false;
  sittingOut = false;

  timedOut = false;
  timedOutCount = 0;

  callValue: any = 0;
  raiseValue: any = 0;
  raiseType: any;

  callReceived: any = 0;
  callAddedValue: any = 0;
  potValue: any = 0;
  potList: any = [];
  cardNumbers: any = [];
  chipsSlider: any = {};

  pokerStatus: any = 'blind';
  pokerPrevStatus: any = 'blind';

  allInStatus: boolean = false;
  myTurn: boolean = false;
  selfWinner = false;

  winnerDeclared = false;
  potDeclared = false;
  winnerSeatNo = 0;

  gameStarted = false;
  countDownFn: any;

  // Toggle
  emojiList: any = [];
  showHandRank: boolean = false;
  showChat: boolean = false;

  chatMessages: any = [];
  chatType: any;
  chatMessage: any = '';
  playerEmoji: any = [];

  constructor(
    public sanitizer: DomSanitizer,
    private router: Router,
    private activatedRoute: ActivatedRoute,

    private modalService: NgbModal,
    private toastrService: ToastrService,

    private accountService: AccountService,
    private gameService: GameService,
    private socketService: SocketService,
  ) { }

  ngOnInit(): void {
    this.userDetails = this.accountService.getUserData();
    this.token = this.activatedRoute.snapshot.queryParams['token'];
    // console.log("this.userDetails", this.userDetails);

    // this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`https://80.209.238.145:5000/sfu/view.html?id=table_1`);
    // this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`https://game.allcardroom.com:5000/sfu/view.html?id=table_1`);
    this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`${this.feedUrl}id=table_1`);

    // Peer init
    if (this.userDetails && this.token) {
      this.peerInit();
    } else {
      this.router.navigate(["game/lobby"])
    }

    // Get Profile
    this.accountService.profileDetails().subscribe({
      next: (result) => {
        this.profileDetails = result.data;
        this.checkUserValid();
      }
    })

    this.socketService.getCameraSettings().subscribe((data: any) => {
      console.log("getCameraSettings", data);
    })

    this.countDownTrigger();
  }

  // Check User Valid
  checkUserValid() {
    this.userJoined = [];

    // Get Game List And Fetch User
    this.gameService.gameList({
      token: this.token
    }).subscribe({
      next: (result) => {
        if (result.data[0]) {
          // let gameDetails = result.data[0];
          this.gameDetails = result.data[0];
          let userValid = false;

          this.gameDetails.game_users.forEach((user: any, index: any) => {
            if (user.user_id == this.userDetails.user_id) {
              userValid = true
              this.myindex = index
              this.playerNo = user.player_no
              this.gameUser = user
              this.betAmount = user.bet_amount

              // console.log("this.gameUser", this.gameUser);
            } else {
              this.otherUserIndexes.push(index)
            }
          })

          if (!userValid) {
            this.router.navigate(["game/lobby"], { queryParams: { token: this.token } })
          } else {
            this.pokerSocketSubscribe(this.gameDetails.id);
            this.setCameraUrls()

            // Fetch Chats
            this.gameService.gameChats({
              game_id: this.gameDetails.id
            }).subscribe({
              next: (result) => {
                this.chatMessages = result.data
              },
              error: (err) => {
                console.log("err", err)
              }
            })

            // Fetch Emojis
            this.gameService.gameEmojis({
              game_id: this.gameDetails.id
            }).subscribe({
              next: (result) => {
                this.emojiList = result.data
              },
              error: (err) => {
                console.log("err", err)
              }
            })
          }
        } else {
          this.toastrService.error('No Game Found');
          this.router.navigate(["game/lobby"], { queryParams: { token: this.token } })
        }
      },
      error: (err) => {
        console.log("err", err)
      }
    })
  }

  pokerSocketSubscribe(id: any) {
    // Join Game
    this.socketService.pokerJoinGame({
      id: id,
      gameId: this.token,
      tableGame: this.gameDetails.table_game == 0 ? false : true,
      userId: this.userDetails.user_id,
      playerNo: this.playerNo,
      betAmount: this.gameUser.bet_amount * 1,
      sittingOut: this.gameUser.sitting_out ? true : false,
      email: this.profileDetails.email,
      username: this.profileDetails.username,
      first_name: this.profileDetails.first_name,
      last_name: this.profileDetails.last_name,
    })

    // ============================================================================
    // ============================================================================

    this.socketService.getSocketId().subscribe((data: any) => {
      // console.log("getSocketId", data);
      this.socketId = data
    })

    // Game Details
    this.socketService.pokerGameDetails().subscribe((data: any) => {
      console.log("pokerGameDetails", data);
      let newUserJoined = this.userJoined.length >= data.players.length ? false : true;
      this.gameData = data;
      this.userJoined = data.players;
      this.potValue = data.potValue;
      this.gameStarted = data.gameStarted;
      this.cardMessage = data.cardMessage;
      this.potList = data.potList ? data.potList : [];

      this.countDownVal = data.timer == undefined ? 60 : data.timer;
      let progWidth = this.countDownVal * 100 / 60;
      this.progressBarWidth = Math.round(progWidth);

      // console.log("data.timer", data.timer)
      // console.log("this.countDownVal", this.countDownVal)

      if (this.gameStarted == false) {
        this.myCards = [];
        this.serveCards = [];
      } else {
        this.serveCards = data.serveCards;

        // let serveCards = this.serveCards;
        // data.serveCards.forEach((cardData: any) => {
        //   if (this.serveCards.length < 5) {
        //     if (!this.serveCards.includes(cardData) && cardData) {
        //       serveCards.push(cardData)
        //     }
        //   }
        // });

        // this.serveCards = serveCards;
      }

      this.otherUserIndexes = [];

      if (data.winnerDeclared) {
        this.winnerDeclared = true;
        setTimeout(() => {
          this.potDeclared = true;
        }, 1000)
      } else {
        this.winnerDeclared = false;
        this.potDeclared = false;
      }

      let maxAmount = 0;
      let totalCount = 0;
      let allInCount = 0;
      this.userJoined.forEach((user: any, index: any) => {
        if (user.winner) {
          this.winnerSeatNo = user.playerNo
        }

        if (user.user_id == this.userDetails.user_id) {
          this.myindex = index
          this.betAmount = user.betAmount
          this.beginingAmount = user.beginingAmount
          this.pokerStatus = user.nextTurn
          this.sittingOut = user.sittingOut
          this.timedOut = user.timedOut
          this.timedOutCount = user.timedOutCount
          this.allInStatus = user.allIn

          if (user.turn) {
            if (user.socketId == this.socketId) {
              this.myTurn = true;
            } else {
              this.myTurn = false;
            }

            this.callValue = data.callValue;
            this.raiseValue = data.callValue + 5;
          } else {
            this.myTurn = false;
          }

          if (user.winner) {
            this.selfWinner = true;
          } else {
            this.selfWinner = false;
          }

          if (this.gameStarted == false) {
            this.myCards = []

            if (this.beginingAmount < 10) {
              // this.quitGame()
              if (this.profileDetails.cash_balance < 100) {
                this.quitGame()
              } else {
                if (!this.sittingOut) {
                  this.sitOutTurn(true)
                  this.buyPopup = true
                }
              }
            }
          } else {
            if (this.myCards.length < 2) {
              this.myCards = user.myCards
            }
          }
        } else {
          this.otherUserIndexes.push(index)

          if (!user.fold && !user.sittingOut) {
            if (maxAmount < user.beginingAmount) {
              maxAmount = user.beginingAmount
            }
          }
        }

        if (!user.fold && !user.sittingOut) {
          totalCount++
          if (user.allIn) { allInCount++ }
        }

        if (newUserJoined) {
          this.gestureControl('None', user.playerNo)
        }
      })

      if (maxAmount < this.beginingAmount) {
        this.maxAmount = maxAmount
      } else {
        this.maxAmount = this.beginingAmount
      }

      console.log("allInCount", allInCount, totalCount)

      if (allInCount >= totalCount - 1 && !this.winnerDeclared && this.pokerStatus == 'check') {
        this.myTurn = false
      }

      // console.log("this.userJoined", this.userJoined)
    });

    // Card Table
    this.socketService.cardTableReceive().subscribe((data: any) => {
      // console.log("cardTableReceive", data);
      let serveCards = this.serveCards;

      let cardData = null;

      this.cardValues.forEach((value: any) => {
        if (value.epc == data.EPC) {
          cardData = value
        }
      })

      if (this.serveCards.length < 5) {
        if (!this.serveCards.includes(cardData) && cardData) {
          serveCards.push(cardData)
        }
      }

      this.serveCards = serveCards;
    })

    // Card User
    this.socketService.cardPlayerReceive().subscribe((data: any) => {
      // console.log("cardPlayerReceive", data);
      let myCards = this.myCards;

      // console.log("cardPlayerReceive data.Mux1", data.Mux1)
      // console.log("cardPlayerReceive this.playerNo", this.playerNo)

      if (data.Mux1 == this.playerNo) {
        let cardData = null;

        this.cardValues.forEach((value: any) => {
          if (value.epc == data.EPC) {
            cardData = value
          }
        })

        // console.log("cardPlayerReceive cardData", cardData)

        if (this.myCards.length < 2) {
          if (!this.myCards.includes(cardData) && cardData) {
            myCards.push(cardData)
          }
        }
      }

      this.myCards = myCards;
    })

    // Get Peer List
    this.socketService.peerUserList().subscribe((data: any) => {
      console.log("peerUserList", data);

      if (data?.players?.length) {
        let peerUserList = data.players;
        this.peerUserList = peerUserList;
      }

      // Modified
      // peerUserList.forEach((user: any) => {
      //   if (user.peerId && user.playerNo != this.playerNo) {
      //     setTimeout(() => {
      //       this.getUserCam(user.peerId, user.playerNo)
      //     }, 5000)
      //   }
      // })
    })

    // Get Camera Details
    this.socketService.getCamDetails().subscribe((data: any) => {
      console.log("getCamDetails", data);
      this.peerUserList.forEach((user: any) => {
        if (user.playerNo == data.playerNo) {
          if (user.peerId && user.playerNo != this.playerNo) {
            // this.getUserCam(user.peerId, user.playerNo)
            this.socketService.peerIdSend({ peerId: this.senderId, playerNo: this.playerNo, publisherSocketId: data.publisherSocketId })
          }
        }
      })
    })

    // Receive PeerId
    this.socketService.peerIdReceived().subscribe((data: any) => {
      console.log("peerIdReceived", data);
      // this.getUserCam(data.peerId, data.playerNo)

      setTimeout(() => {
        this.getUserCam(data.peerId, data.playerNo)
      }, 1000)
    })

    // Reload Page
    this.socketService.reloadPage().subscribe((data: any) => {
      console.log("reloadPage", data);
      window.location.reload();
    })

    // Chat Message
    this.socketService.receiveChatMessage().subscribe((data: any) => {
      let chatMessages = this.chatMessages;
      chatMessages.push(data);
      this.chatMessages = chatMessages;

      if (data.chat_type == 'emoji') {
        this.playerEmoji.push(data)

        setTimeout(() => {
          let dataRemoved = false;

          this.playerEmoji.forEach((message: any, index: any) => {
            if (dataRemoved == false) {
              if (message.from_player_no == data.from_player_no) {
                this.playerEmoji.splice(index, 1)
                dataRemoved = true
              }
            }
          })

        }, 5000);
      }

      this.chatScrollBottom();
    })

    // Poker Audio
    this.socketService.pokerAudio().subscribe((data: any) => {
      console.log("pokerAudio", data)
      if (data == 'winner') {
        if (this.selfWinner && this.winnerDeclared) { this.playAudio(data) }
      }

      else if (data == 'not-winner-other_players') {
        if (!this.selfWinner && this.winnerDeclared) { this.playAudio(data) }
      }

      else { this.playAudio(data) }
    })

    // Poker Turn Play
    this.socketService.pokerTurnPlay().subscribe((data: any) => {
      console.log("pokerTurnPlay", data)
      if (data.chipsSlide && data.playerNo) {
        setTimeout(() => {
          this.chipsSlider[data.playerNo] = { showChips: true, slideChips: false }
          setTimeout(() => {
            this.chipsSlider[data.playerNo] = { showChips: true, slideChips: true }
          }, 500)
          setTimeout(() => {
            this.chipsSlider[data.playerNo] = { showChips: false, slideChips: false }
          }, 3000)
        }, 3000)
      }
    })

    // Dealer Game Reset
    this.socketService.dealerGameReset().subscribe((data: any) => {
      Swal.fire({
        icon: 'error',
        title: 'Game Reset',
        text: 'Dealer have reset the game.',
        timer: 3000
      })
    })

    // Gesture Set
    this.setGestureSocket()
  }

  // Poker Actions
  // ================================================

  // Blind
  blind() {
    // this.gestureControl('AllIn', this.playerNo)

    this.socketService.pokerBlindSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
    })
  }

  // Bet
  bet() {
    console.log("bet", this.raiseValue)

    // this.callValue = this.raiseValue;

    // this.socketService.pokerBetSend({
    //   playerindex: this.myindex,
    //   gameId: this.token,
    //   userId: this.userDetails.user_id,
    //   amount: this.callValue,
    //   callValue: this.callValue,
    //   cardNumbers: this.cardNumbers,
    // })

    // this.myTurn = false;
    // this.pokerStatus = 'call';
    this.callValue = this.raiseValue;

    this.socketService.pokerCallSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      amount: this.callValue,
      callValue: this.callValue,
      type: this.pokerPrevStatus,
      bet: true
    })

    this.myTurn = false;
  }

  // Call
  call(val: any) {
    let newVal = this.callValue - this.callAddedValue;
    this.potValue = this.potValue + newVal;

    this.socketService.pokerCallSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      amount: this.callValue,
      callValue: this.callValue,
      type: this.pokerPrevStatus
    })

    this.pokerPrevStatus = 'call';
    this.myTurn = false;
  }

  // Check
  check() {
    this.socketService.pokerCheckSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      // cards: cards,
      myCards: this.myCards
    })

    this.pokerPrevStatus = 'check';

    this.myTurn = false;
  }

  // Straddle
  straddle() {
    this.socketService.pokerStraddleSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      amount: this.callValue,
      callValue: 20,
      type: this.pokerPrevStatus,
      bet: true
    })

    this.pokerPrevStatus = 'straddle';

    this.myTurn = false;
  }

  // Fold
  fold() {
    this.socketService.pokerFoldSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id
    })

    this.myTurn = false;
  }

  // All In
  allIn() {
    // this.socketService.pokerAllInSend({
    //   playerindex: this.myindex,
    //   gameId: this.token,
    //   userId: this.userDetails.user_id,
    //   callValue: this.beginingAmount
    // })

    this.socketService.pokerCallSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      amount: this.beginingAmount,
      callValue: this.beginingAmount,
      type: this.pokerPrevStatus
    })
  }

  // Raise
  raise() {
    console.log("raise", this.raiseValue)

    // this.callValue = this.callValue + 5;
    this.callValue = this.raiseValue;

    this.socketService.pokerCallSend({
      playerindex: this.myindex,
      gameId: this.token,
      userId: this.userDetails.user_id,
      amount: this.callValue,
      callValue: this.callValue,
      type: this.pokerPrevStatus
    })

    this.myTurn = false;
  }

  // Raise Pop
  raisePop(content: any, type: any) {
    this.modalService.open(content).result.then((result) => { });

    this.raiseType = type;
  }

  raiseSubmit() {
    if (this.pokerStatus == 'call') {
      this.raise();
    }

    if (this.pokerStatus == 'check') {
      this.bet();
    }
  }

  raiseModalSubmit(close: any) {
    if (this.raiseType == 'raise') {
      this.raise();
    }

    if (this.raiseType == 'bet') {
      this.bet();
    }

    close();
  }

  // ================================================

  showHandCards(index: any) {
    let show = false;

    if (this.getUserDetails(index + 1)?.sittingOut) {
      show = false
      return
    }

    if (this.playerNo == index + 1) {
      show = true
    } else if (this.getUserDetails(index + 1)?.allIn) {
      show = true
    } else if (this.getUserDetails(index + 1)?.fold) {
      show = false
    } else if (this.winnerDeclared) {
      show = true
    }

    return show
  }

  getSelfStatus() {
    let status = "";
    let cards: any[] = []

    if (this.serveCards.length > 0) {
      this.serveCards.forEach((card: any) => {
        if (!cards.includes(card.code)) {
          cards.push(card.code)
        }
        // cards.push(card.code)
      })
    }

    if (this.myCards.length > 0) {
      this.myCards.forEach((card: any) => {
        if (!cards.includes(card.code)) {
          cards.push(card.code)
        }
        // cards.push(card.code)
      })
    }

    if (cards.length > 0) {
      let hand = Hand.solve(cards);
      status = hand.name
    }

    return status
  }

  async declareWinner() {
    // let serveCards = this.serveCards.map((card: any) => {
    //   return card.code
    // })

    // await this.userJoined.forEach(async (user: any, index: any) => {
    //   let cards: any[] = []

    //   if (user.myCards) {
    //     cards = user.myCards.map((card: any) => {
    //       return card.code
    //     })
    //   }

    //   cards = [...serveCards, ...cards];

    //   this.userJoined[index].handStatus = await Hand.solve(cards);
    // })

    // console.log("userJoined", this.userJoined)

    // let winner = await Hand.winners([this.userJoined[0].handStatus, this.userJoined[1].handStatus]);
    // console.log("winner", winner)

    // this.userJoined.forEach((user: any, index: any) => {
    //   if (winner[0] == user.handStatus) {
    //     console.log("user", user);
    //     console.log("index", index);

    //     this.userJoined[index].winner = true;
    //     if (index == this.myindex) {
    //       this.selfWinner = true
    //     }
    //     this.winnerDeclared = true;
    //   }
    // })
  }

  // ================================================

  // Check
  checkUserFold(index: any) {
    if (this.userJoined[index]) {
      if (this.userJoined[index].fold) {
        return true
      }
    }

    return false
  }

  getUserDetails(index: any) {
    let userData = null;

    if (this.userJoined.length > 10) {
      return this.userJoined[10]
    }

    this.userJoined.forEach((user: any) => {
      if (user.playerNo == index) {
        userData = user
      }
    })

    return userData
  }

  showType(index: any, type: any, notype: any) {
    let show = false;
    let userData = null;

    if (this.userJoined.length > 10) {
      return this.userJoined[10]
    }

    this.userJoined.forEach((user: any) => {
      if (user.playerNo == index) {
        userData = user
      }
    })

    if (this.gameStarted && userData) {
      if (userData[type]) {
        show = true

        if (notype) {
          if (userData[notype]) {
            show = false
          }
        }
      }
    }

    return show
  }

  // Get RFID Value
  getCardValue(id: any) {
    return this.cardValues.find((card: any) => {
      return card.epc == id
    })
  }

  // Sit Out
  sitOutTurn(type: any) {
    this.socketService.sittingOutSet({ playerNo: this.playerNo, sittingOut: type });
    this.sittingOut = type;

    this.gameService.gameUserSittingUpdate({
      token: this.token,
      sitting_out_status: type
    }).subscribe({
      next: (result) => {
        if (result.status) { }
      },
      error: (err) => {
        console.log("err", err)
      }
    })
  }

  // 
  timedOutTurn(type: any) {
    this.socketService.timedOutPopupSet({ playerNo: this.playerNo, leaveTable: type });
    this.timedOut = false;
    this.timedOutCount = 0;
  }

  // Peer Init
  peerInit() {
    // Peer Open
    navigator.mediaDevices.getUserMedia({
      video: { width: 384, height: 264 },
      audio: false
    }).then((stream) => {
      this.mySteam = stream;
    }).catch((err) => {
      console.log(err);
    });

    this.peer.on("open", (id: any) => {
      this.senderId = id
      console.log(`this.peer.on("open")`, id)

      if (this.senderId) {
        // this.toggleWebcam()
        this.updatePeer()
      }

      setTimeout(() => {
        console.log('this.userJoined', this.userJoined)

        let playerAdded: any[] = [];
        this.userJoined.forEach((user: any) => {
          if (user.showCam) {
            playerAdded.push(user.playerNo)
          }
        })

        this.peerUserList.forEach((user: any) => {
          console.log('user', user)
          if (playerAdded.includes(user.playerNo)) {
            this.socketService.peerIdSend({ peerId: this.senderId, playerNo: this.playerNo, publisherSocketId: user.socketId })
          }
        })
      }, 2000)
    })

    // Peer Call
    this.peer.on("call", (call: any) => {
      console.log('this.peer.on("call")', call);
      // console.log('this.mySteam', this.mySteam);

      // console.log('_remoteStream', call["_remoteStream"] || call._remoteStream);
      // console.log('_localStream', call["_localStream"] || call._localStream);

      // this.table_view_cam_6 = document.getElementById(`table_view_cam6`);
      // this.table_view_cam_1.srcObject = call.remoteStream

      // navigator.mediaDevices.getUserMedia({
      //   video: { width: 384, height: 264 },
      //   audio: false
      // }).then((stream) => {
      //   this.mySteam = stream;
      // }).catch((err) => {
      //   console.log(err);
      // });

      if (this.mySteam) {
        call.answer(this.mySteam);
        console.log('call.answer(this.mySteam);', this.mySteam);
      } else {
        call.answer(new MediaStream())
      }

      call.on("stream", (remoteStream: any) => {
        console.log("remoteStream _remoteStream", remoteStream)
        console.log("remoteStream _remoteStream", remoteStream.id)

        let playerNo = null

        this.userJoined.forEach((item: any) => {
          console.log(`${item.stream_id}`, `${item.stream_id}` == remoteStream.id)
          if (`${item.stream_id}` == remoteStream.id) {
            playerNo = item.playerNo
          }
        })

        console.log("playerNo", playerNo)

        if (playerNo == 1) {
          this.table_view_cam_1 = document.getElementById(`table_view_cam1`);
          this.table_view_cam_1.srcObject = remoteStream
        }
        else if (playerNo == 2) {
          this.table_view_cam_2 = document.getElementById(`table_view_cam2`);
          this.table_view_cam_2.srcObject = remoteStream
        }
        else if (playerNo == 3) {
          this.table_view_cam_3 = document.getElementById(`table_view_cam3`);
          this.table_view_cam_3.srcObject = remoteStream
        }
        else if (playerNo == 4) {
          this.table_view_cam_4 = document.getElementById(`table_view_cam4`);
          this.table_view_cam_4.srcObject = remoteStream
        }
        else if (playerNo == 5) {
          this.table_view_cam_5 = document.getElementById(`table_view_cam5`);
          this.table_view_cam_5.srcObject = remoteStream
        }
        else if (playerNo == 6) {
          this.table_view_cam_6 = document.getElementById(`table_view_cam6`);
          this.table_view_cam_6.srcObject = remoteStream
        }
        else if (playerNo == 7) {
          this.table_view_cam_7 = document.getElementById(`table_view_cam7`);
          this.table_view_cam_7.srcObject = remoteStream
        }
        else if (playerNo == 8) {
          this.table_view_cam_8 = document.getElementById(`table_view_cam8`);
          this.table_view_cam_8.srcObject = remoteStream
        }
        else if (playerNo == 9) {
          this.table_view_cam_9 = document.getElementById(`table_view_cam9`);
          this.table_view_cam_9.srcObject = remoteStream
        }

        // if (playerNo == 1) {
        //   this.table_view_cam_1.srcObject = remoteStream
        // } else if (playerNo == 2) {
        //   this.table_view_cam_2.srcObject = remoteStream
        // } else if (playerNo == 3) {
        //   this.table_view_cam_3.srcObject = remoteStream
        // } else if (playerNo == 4) {
        //   this.table_view_cam_4.srcObject = remoteStream
        // } else if (playerNo == 5) {
        //   this.table_view_cam_5.srcObject = remoteStream
        // } else if (playerNo == 6) {
        //   this.table_view_cam_6.srcObject = remoteStream
        // } else if (playerNo == 7) {
        //   this.table_view_cam_7.srcObject = remoteStream
        // } else if (playerNo == 8) {
        //   this.table_view_cam_8.srcObject = remoteStream
        // } else if (playerNo == 9) {
        //   this.table_view_cam_9.srcObject = remoteStream
        // }
      });
    });

    // this.peer.on('connection', function (data) {
    //   console.log('peer.on("connection")', data);
    // });

    // this.peer.on('close', function () {
    //   console.log('peer.on("close")');
    // });

    // this.peer.on('disconnected', function (data) {
    //   console.log('peer.on("disconnected")', data);
    // });

    // this.peer.on('error', function (err) {
    //   console.log('peer.on("error")', err);
    // });
  }

  createEmptyAudioTrack = (): MediaStreamTrack => {
    const ctx = new AudioContext();
    const oscillator = ctx.createOscillator();
    const destination = ctx.createMediaStreamDestination();
    oscillator.connect(destination);
    oscillator.start();
    const track = destination.stream.getAudioTracks()[0];
    return Object.assign(track, { enabled: false }) as MediaStreamTrack;
  }

  createEmptyVideoTrack = ({ width = 640, height = 480 }): MediaStreamTrack => {
    const canvas = Object.assign(document.createElement('canvas'), { width, height });
    canvas.getContext('2d')?.fillRect(0, 0, width, height);
    const stream = canvas.captureStream();
    const track = stream.getVideoTracks()[0];
    return Object.assign(track, { enabled: false }) as MediaStreamTrack;
  };

  getUserCam(peerId: any, playerNo: any) {
    if (!this.table_view_cam) {
      this.table_view_cam = document.getElementById('table_view_cam');
    }

    if (playerNo == 1 && !this.table_view_cam_1) {
      this.table_view_cam_1 = document.getElementById(`table_view_cam1`);
    }
    else if (playerNo == 2 && !this.table_view_cam_2) {
      this.table_view_cam_2 = document.getElementById(`table_view_cam2`);
    }
    else if (playerNo == 3 && !this.table_view_cam_3) {
      this.table_view_cam_3 = document.getElementById(`table_view_cam3`);
    }
    else if (playerNo == 4 && !this.table_view_cam_4) {
      this.table_view_cam_4 = document.getElementById(`table_view_cam4`);
    }
    else if (playerNo == 5 && !this.table_view_cam_5) {
      this.table_view_cam_5 = document.getElementById(`table_view_cam5`);
    }
    else if (playerNo == 6 && !this.table_view_cam_6) {
      this.table_view_cam_6 = document.getElementById(`table_view_cam6`);
    }
    else if (playerNo == 7 && !this.table_view_cam_7) {
      this.table_view_cam_7 = document.getElementById(`table_view_cam7`);
    }
    else if (playerNo == 8 && !this.table_view_cam_8) {
      this.table_view_cam_8 = document.getElementById(`table_view_cam8`);
    }
    else if (playerNo == 9 && !this.table_view_cam_9) {
      this.table_view_cam_9 = document.getElementById(`table_view_cam9`);
    }

    if (this.mySteam) {
      console.log("this.mySteam", true)
      let stream = this.mySteam;

      let call = this.peer.call(peerId, stream);

      call.on("stream", (remoteStream) => {
        console.log("remoteStream", remoteStream)

        if (playerNo == 1) {
          this.table_view_cam_1.srcObject = remoteStream
        }
        else if (playerNo == 2) {
          this.table_view_cam_2.srcObject = remoteStream
        }
        else if (playerNo == 3) {
          this.table_view_cam_3.srcObject = remoteStream
        }
        else if (playerNo == 4) {
          this.table_view_cam_4.srcObject = remoteStream
        }
        else if (playerNo == 5) {
          this.table_view_cam_5.srcObject = remoteStream
        }
        else if (playerNo == 6) {
          this.table_view_cam_6.srcObject = remoteStream
        }
        else if (playerNo == 7) {
          this.table_view_cam_7.srcObject = remoteStream
        }
        else if (playerNo == 8) {
          this.table_view_cam_8.srcObject = remoteStream
        }
        else if (playerNo == 9) {
          this.table_view_cam_9.srcObject = remoteStream
        }
      });
    }

    // else {
    //   let call = this.peer.call(peerId, new MediaStream())

    //   console.log("!this.mySteam call", call)

    //   call.on("stream", (remoteStream) => {
    //     console.log("remoteStream", remoteStream)

    //     if (playerNo == 1) {
    //       this.table_view_cam_1.srcObject = remoteStream
    //     } else if (playerNo == 2) {
    //       this.table_view_cam_2.srcObject = remoteStream
    //     } else if (playerNo == 3) {
    //       this.table_view_cam_3.srcObject = remoteStream
    //     } else if (playerNo == 4) {
    //       this.table_view_cam_4.srcObject = remoteStream
    //     } else if (playerNo == 5) {
    //       this.table_view_cam_5.srcObject = remoteStream
    //     } else if (playerNo == 6) {
    //       this.table_view_cam_6.srcObject = remoteStream
    //     } else if (playerNo == 7) {
    //       this.table_view_cam_7.srcObject = remoteStream
    //     } else if (playerNo == 8) {
    //       this.table_view_cam_8.srcObject = remoteStream
    //     } else if (playerNo == 9) {
    //       this.table_view_cam_9.srcObject = remoteStream
    //     }
    //   });
    // }
  }

  // Update Peer
  updatePeer() {
    // console.log("updatePeer")

    this.gameService.gameUserPeerUpdate({
      token: this.token,
      peer_id: this.senderId
    }).subscribe({
      next: (result) => {
        // this.getGameList()
        this.socketService.pokerPeerIdSend({
          peerId: this.senderId,
          playerNo: this.playerNo
        })
      },
      error: (err) => {
        console.log("err", err)
      }
    })
  }

  // Get Game List And Fetch User **Not Needed
  getGameList() {
    this.gameService.gameList({
      token: this.token
    }).subscribe({
      next: (result) => {
        if (result.data[0]) {
          let gameDetails = result.data[0];
          let otherUser = "";
          gameDetails.game_users.forEach((user: any) => {
            if (user.peer_id != this.senderId) {
              otherUser = user.peer_id
            }
          })

          if (otherUser) {
            // this.peerCallUser(otherUser)
          }
        }
      },
      error: (err) => {
        console.log("err", err)
      }
    })
  }

  // Quit Game
  quitGame() {
    console.log("quitGame", this.gameDetails)

    this.gameService.gameUserQuit({
      game_id: this.gameDetails.id,
    }).subscribe({
      next: (result) => {
        this.router.navigate(["game/lobby"], { queryParams: { token: this.token } }).then(() => {
          window.location.reload();
        }).catch(() => { });
      },
      error: (err) => {
        console.log("err", err)
      }
    })
  }

  // Add Game Coins
  addGameCoins() {
    this.gameService.gameBuyCoin({
      gameUserId: this.gameUser.id,
      amount: this.buyAmount
    }).subscribe({
      next: (result) => {
        this.buyPopup = false
        this.checkUserValid()

        setTimeout(() => {
          if (!this.gameStarted && this.beginingAmount > 10) {
            this.sitOutTurn(false)
          }
        }, 2000)
      },
      error: (err) => {
        console.log("err", err)

        if (err.error?.message) {
          Swal.fire('Error', err.error?.message, 'error')
        }
      }
    })
  }

  // Peer Call User **Not Needed
  peerCallUser(otherUserId: any) {
    this.showWebcam = true;
    this.table_view_cam = document.getElementById('table_view_cam');

    navigator.mediaDevices.getUserMedia({
      video: { width: 384, height: 264 },
      audio: false
    }).then((stream) => {
      // this.table_view_cam.srcObject = stream;

      let call = this.peer.call(otherUserId, stream);

      call.on("stream", (remoteStream) => {
        // this.table_view_cam5.srcObject = remoteStream
      });
    }).catch((err) => {
      console.log(err);
    });
  }

  // Webcam
  toggleWebcam() {
    if (this.showCam) {
      this.showCam = false
      this.socketService.webcamToggleSend({ playerNo: this.playerNo, showCam: false });
    } else {
      // this.showCam = true
      // this.socketService.webcamToggleSend({ playerNo: this.playerNo, showCam: true });

      navigator.mediaDevices.getUserMedia({
        video: { width: 384, height: 264 },
        audio: false
      }).then((stream) => {
        this.mySteam = stream;
        this.showCam = true
        this.socketService.webcamToggleSend({ playerNo: this.playerNo, stream_id: stream.id, showCam: true });

        console.log("stream", stream.id)

        this.table_view_cam = document.getElementById('table_view_cam');

        if (this.playerNo) {
          if (this.playerNo == 1) {
            this.table_view_cam_1 = document.getElementById(`table_view_cam1`);
            this.table_view_cam_1.srcObject = stream
          } else if (this.playerNo == 2) {
            this.table_view_cam_2 = document.getElementById(`table_view_cam2`);
            this.table_view_cam_2.srcObject = stream
          } else if (this.playerNo == 3) {
            this.table_view_cam_3 = document.getElementById(`table_view_cam3`);
            this.table_view_cam_3.srcObject = stream
          } else if (this.playerNo == 4) {
            this.table_view_cam_4 = document.getElementById(`table_view_cam4`);
            this.table_view_cam_4.srcObject = stream
          } else if (this.playerNo == 5) {
            this.table_view_cam_5 = document.getElementById(`table_view_cam5`);
            this.table_view_cam_5.srcObject = stream
          } else if (this.playerNo == 6) {
            this.table_view_cam_6 = document.getElementById(`table_view_cam6`);
            this.table_view_cam_6.srcObject = stream
          } else if (this.playerNo == 7) {
            this.table_view_cam_7 = document.getElementById(`table_view_cam7`);
            this.table_view_cam_7.srcObject = stream
          } else if (this.playerNo == 8) {
            this.table_view_cam_8 = document.getElementById(`table_view_cam8`);
            this.table_view_cam_8.srcObject = stream
          } else if (this.playerNo == 9) {
            this.table_view_cam_9 = document.getElementById(`table_view_cam9`);
            this.table_view_cam_9.srcObject = stream
          }
        }
      }).catch((err) => {
        console.log(err);

        Swal.fire('Error', "Webcam is not available!", 'error')
      });

    }
  }

  // Countdown
  countDownTrigger() {
    let totalCount = 60
    this.countDownVal = totalCount;

    this.countDownFn = setInterval(() => {
      if (this.countDownVal > 0) {
        this.countDownVal = this.countDownVal - 1

        let progWidth = this.countDownVal * 100 / totalCount;
        this.progressBarWidth = Math.round(progWidth)
      }

      // if (this.countDownVal == 0 && this.myTurn) {
      //   this.fold()
      // }

      if (this.timedOutCount > 0) {
        this.timedOutCount = this.timedOutCount - 1
      }
    }, 1000)
  }

  // PLAY AUDIO
  playAudio(type: string) {
    let audio = new Audio();

    switch (type) {
      case 'deal_each_card':
        audio.src = "assets/audio/Dealing_each_card.wav";
        break;
      case 'fold_cards':
        audio.src = "assets/audio/Folding_Cards.wav";
        break;
      case 'push_chips_to_winner':
        audio.src = "assets/audio/Dealer_Pushing_chips_to_the_winner.wav";
        break;

      // New Audio
      case 'blind_bet_call_raise':
        audio.src = "assets/audio/blind_bet_call_raise.wav";
        break;
      case 'deal_each_card_and_fold_cards':
        audio.src = "assets/audio/deal_each_card_and_fold_cards.mp3";
        break;
      case 'winner':
        audio.src = "assets/audio/winner.wav";
        break;
      case 'not-winner-other_players':
        audio.src = "assets/audio/not-winner-other_players.wav";
        break;
    }

    // return

    var load = audio.load();
    var play = audio.play();

    if (play !== undefined) {
      play.then(_ => {
        // Autoplay started!
      }).catch(error => {
        console.log("error", error)
        // Autoplay was prevented.
        // Show a "Play" button so that user can start playback.
      });
    }
  }

  getUserClass(playerNo: any) {
    let className = "";

    if (playerNo == 1) {
      className = 'box-6'
    } else if (playerNo == 2) {
      className = 'box-7'
    } else if (playerNo == 3) {
      className = 'box-8'
    } else if (playerNo == 4) {
      className = 'box-9'
      // className = 'd-none'
    } else if (playerNo == 5) {
      className = 'd-none'
    } else if (playerNo == 6) {
      className = 'box-1'
      // className = 'd-none'
    } else if (playerNo == 7) {
      className = 'box-2'
    } else if (playerNo == 8) {
      className = 'box-3'
    } else if (playerNo == 9) {
      className = 'box-4'
    }

    return className;
  }

  getUserPotClass(playerNo: any) {
    let className = "";

    if (playerNo == 1) {
      className = 'player-1'
    } else if (playerNo == 2) {
      className = 'player-2'
    } else if (playerNo == 3) {
      className = 'player-3'
    } else if (playerNo == 4) {
      // className = 'd-none'
      className = 'player-4'
    } else if (playerNo == 5) {
      className = 'd-none'
    } else if (playerNo == 6) {
      // className = 'd-none'
      className = 'player-6'
    } else if (playerNo == 7) {
      className = 'player-7'
    } else if (playerNo == 8) {
      className = 'player-8'
    } else if (playerNo == 9) {
      className = 'player-9'
    }

    return className;
  }

  // Camera View Change
  changeCameraView(type: any) {
    this.socketService.sendCameraControl({ type });
  }

  setCameraUrls() {
    let type = 'demo';
    if (environment.production) {
      type = 'live'
    }

    let setUrl = `https://80.209.238.145:5000/sfu/?id=${this.token}_${this.playerNo}_${type}`;
    this.cameraUrl = this.sanitizer.bypassSecurityTrustResourceUrl(setUrl);

  }

  // 
  toggleChat(type: any) {
    if (this.showChat && this.chatType == type) {
      this.showChat = false
    } else {
      this.showChat = true
    }

    this.chatType = type

    this.chatScrollBottom();
  }

  sendChatMessage() {
    if (this.chatMessage) {
      this.socketService.sendChatMessage({
        gameId: this.token,
        message: this.chatMessage,

        game_id: this.gameDetails.id,
        game_user_id: this.gameUser.id,
        from_id: this.gameUser.user_id,
        from_type: 'player',
        from_player_no: this.playerNo,
        chat_type: 'chat',
      });
      this.chatMessage = '';
    }
  }

  sendEmoji(emoji: any) {
    this.socketService.sendChatMessage({
      gameId: this.token,
      message: '',

      game_id: this.gameDetails.id,
      game_user_id: this.gameUser.id,
      from_id: this.gameUser.user_id,
      from_type: 'player',
      from_player_no: this.playerNo,
      chat_type: 'emoji',
      emoji_id: emoji.id,

      game_emoji: emoji
    });

    // this.showChat = false
    // this.chatType = 'message'
  }

  selfEmoji(player_no: any) {
    let emojiUrl: string | null = null;

    this.playerEmoji.forEach((emoji: any) => {
      if (emoji.from_player_no == player_no) {
        // emojiUrl = emoji
        if (emoji.game_emoji?.file_name) {
          emojiUrl = this.emojiUrl + emoji.game_emoji?.file_name
        }
      }
    })

    return emojiUrl;
  }

  chatScrollBottom() {
    setTimeout(() => {
      const chatList = document.getElementById('chatList');
      if (chatList) {
        chatList.scrollTop = chatList.scrollHeight;
      }
    }, 100)
    setTimeout(() => {
      const chatList = document.getElementById('chatList');
      if (chatList) {
        chatList.scrollTop = chatList.scrollHeight;
      }
    }, 500)
  }

  // Gestures Control
  setGestureSocket() {
    // let allVideos = [...document.getElementsByClassName('generatedVideo')];

    let allVideos = Array.from(document.getElementsByClassName('generatedVideo'));

    allVideos.forEach((video: any) => {
      console.log("video", video)
    })

    this.socketService.gestureReceive().subscribe((data: any) => {
      console.log("gestureReceive", data)
      this.gestureControl(data.type, data.playerNo)
    })
  }

  gestureControl(type: any, playerNo: any) {
    let gestureList = {
      None: { start: 0.0, end: 0.0 },
      Angry: { start: 0.0, end: 3.5 },
      Bet: { start: 3.5, end: 8 },
      FakeBetCheck: { start: 8.5, end: 11.5 },
      Disbelief: { start: 11.5, end: 16.0 },
      LookAtCards: { start: 17.0, end: 22.0 },
      Check: { start: 22.0, end: 25.0 },
      Happy: { start: 26.0, end: 30.0 },
      AllIn: { start: 30.0, end: 40.0 },
    }

    let videoId = `video_character${playerNo}_seat1`;
    let video = document.getElementById(videoId) as HTMLVideoElement;
    let startTime = (gestureList as any)[type].start;
    let endTime = (gestureList as any)[type].end;

    console.log("videoId", videoId)
    console.log("video", video)

    if (video) {
      video.loop = false;
      video.currentTime = startTime;
      video.playbackRate = 1.5;

      if (type != "None") {
        console.log(`Playing from ${startTime}`);
        console.log(`PlaybackRate ${video.playbackRate}`)

        video.play();
        let delayTime = (endTime - startTime) * 1000

        setTimeout(() => {
          video.pause();
          video.currentTime = endTime;
          console.log(`Paused at ${endTime}`);

          this.gestureControl("None", playerNo)
        }, delayTime);
      } else {
        setTimeout(() => {
          video.pause();
          video.currentTime = endTime;
        }, 100);
      }
    }
  }
}