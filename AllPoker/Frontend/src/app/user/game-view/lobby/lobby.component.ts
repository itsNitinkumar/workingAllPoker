import { Component, ViewChild } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeResourceUrl, } from '@angular/platform-browser';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { Peer } from "peerjs";
import { ToastrService } from 'ngx-toastr';
import Swal from 'sweetalert2';

import { AccountService } from '../../_services/account.service';
import { GameService } from '../../_services/game.service';

// Game View
import { environment } from 'src/environments/environment';
import { rfidValues } from 'src/rfid_values';
import { SocketService } from '../../_services/socket.service';

@Component({
  selector: 'app-lobby',
  templateUrl: './lobby.component.html',
  styleUrls: ['./lobby.component.css']
})

export class LobbyComponent {
  @ViewChild('content') content: any;

  feedUrl = environment.feedUrl;

  backgroundUrl: SafeResourceUrl | undefined;


  token: any;
  availSeat: boolean = false;

  userData: any;
  profileDetails: any;

  modalReference: any;
  closeResult = '';
  table_view_video: any;
  videoSource: any;
  seatNo: any;
  gameUsers: any = [];

  betAmount: any;

  // ====================
  // Player Game Details
  // ====================
  @ViewChild("me") me: any;
  @ViewChild("remote") remote: any;
  emojiUrl = environment.emojiUrl;
  cardValues = rfidValues.cards;

  userDetails: any;
  gameDetails: any;
  cameraUrl: SafeResourceUrl | undefined;
  showCam: boolean = false;

  userJoined: any = [];
  playerEmoji: any = [];
  cardMessage: any = "";


  // Poker Gameplay
  // ===============================================
  gameData: any = {};
  serveCards: any = [];

  socketId: any;
  beginingAmount: any = 0;

  otherUserIndexes: any = [];
  countDownVal = 60;
  progressBarWidth = 100;

  callValue: any = 0;
  raiseValue: any = 0;
  raiseType: any;

  callReceived: any = 0;
  callAddedValue: any = 0;
  potValue: any = 0;
  cardNumbers: any = [];

  pokerStatus: any = 'blind';
  pokerPrevStatus: any = 'blind';

  winnerDeclared = false;
  winnerSeatNo = 0;

  gameStarted = false;
  countDownFn: any;

  // Peer 
  // ===============================================
  peerConfig = {
    host: environment.peer_host,
    port: environment.peer_port,
    path: environment.peer_path,
    config: environment.peer_config,
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
  mySteam: any;
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


  constructor(
    private router: Router,
    private activatedRoute: ActivatedRoute,
    public sanitizer: DomSanitizer,

    private modalService: NgbModal,
    private toastrService: ToastrService,

    private accountService: AccountService,
    private gameService: GameService,
    private socketService: SocketService,
  ) { }

  ngOnInit(): void {
    this.userDetails = this.accountService.getUserData();
    this.table_view_video = document.getElementById('lobby_user_vid');
    this.userData = this.accountService.getUserData();

    // this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`https://game.allcardroom.com:5000/sfu/view.html?id=table_1`);
    this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`${this.feedUrl}id=table_1`);
    // this.backgroundUrl = "";

    if (this.activatedRoute?.snapshot?.queryParams) {
      if (this.activatedRoute.snapshot.queryParams['token']) {
        this.token = this.activatedRoute.snapshot.queryParams['token']
      }
    }

    this.accountService.profileDetails().subscribe({
      next: (result) => {
        this.profileDetails = result.data;
      }
    })

    if (this.token && this.userData) {
      this.getGameDetails();
      this.peerInit();
    } else {
      // this.toastrService.error('No Token Found');
      Swal.fire('Error', 'No Token Found', 'error')
    }

    this.betAmount = 1000;
  }

  // Get Game Details
  getGameDetails() {
    this.gameService.findGameOrCreate({
      token: this.token
    }).subscribe({
      next: (result) => {
        if (result.status) {
          let userAlreadyExist = false;

          this.gameUsers = result.data?.game_users;
          result.data.game_users.forEach((user: any) => {
            if (user.user_id == this.userData.user_id) {
              userAlreadyExist = true
            }
          })

          if (userAlreadyExist) {
            this.router.navigate(['/game/table-view'], { queryParams: { token: this.token } })
          } else {
            // this.modalReference = this.modalOpen(this.content);

            if (result.data.game_users.length < 9) {
              this.availSeat = true;
              this.pokerSocketSubscribe(result.data.id);
              this.countDownTrigger();
            }
          }
        }
      }
    })
  }

  // Add User To Game
  addUserToGame(closeModal: any) {
    closeModal()
    this.gameService.gameUserCreate({
      playerNo: this.seatNo,
      token: this.token,
      bet_amount: this.betAmount
    }).subscribe({
      next: (result) => {
        if (result.status) {
          this.router.navigate(['/game/table-view'], { queryParams: { token: this.token } })
        }
      },
      error: (err) => {
        console.log("err", err)

        if (err.error?.message) {
          // this.toastrService.error(err.error?.message);
          Swal.fire('Error', err.error?.message, 'error')
        }
      }
    })
  }

  // Open Modal
  modalOpen(seatNo: any, content: any) {

    if (this.gameStarted) {
      // this.toastrService.error('Game Started! Wait for next game');
      Swal.fire('Game Started!', 'Wait for next game', 'error')
      return
    }

    this.seatNo = seatNo;

    if (this.checkSeatOpen(seatNo)) {
      this.modalService.open(content, { windowClass: 'no-background' });
      this.table_view_video = document.getElementById('lobby_user_vid');

      navigator.mediaDevices.getUserMedia({
        video: { width: 384, height: 264 },
        audio: false
      }).then((stream) => {
        this.table_view_video.srcObject = stream
      }).catch((err) => {
        console.log(err);
      });
    } else {
      // this.toastrService.error('Seat Not Available');
      Swal.fire('Error', 'Seat Not Available', 'error')
    }
  }

  checkSeatOpen(playerNo: any) {
    let userAlreadyExist = false;

    this.gameUsers.forEach((user: any) => {
      if (user.player_no == playerNo) {
        userAlreadyExist = true
      }
    })

    if (!userAlreadyExist) {
      return true
    } else {
      return false
    }
  }


  // Start Game
  pokerSocketSubscribe(id: any) {
    // Join Game
    this.socketService.pokerTableGroupJoin({
      id: id,
      gameId: this.token,
      userId: this.userDetails.user_id,
    })

    // ============================================================================
    // ============================================================================

    this.socketService.getSocketId().subscribe((data: any) => {
      console.log("getSocketId", data);
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

      this.countDownVal = data.timer == undefined ? 60 : data.timer;
      let progWidth = this.countDownVal * 100 / 60;
      this.progressBarWidth = Math.round(progWidth);

      if (this.gameStarted == false) {
        this.serveCards = [];
      } else {
        this.serveCards = data.serveCards;
      }

      this.otherUserIndexes = [];

      if (data.winnerDeclared) {
        this.winnerDeclared = true;
      } else {
        this.winnerDeclared = false;
      }

      this.userJoined.forEach((user: any, index: any) => {
        if (user.winner) {
          this.winnerSeatNo = user.playerNo
        }

        this.otherUserIndexes.push(index)

        if (newUserJoined) {
          this.gestureControl('None', user.playerNo)
        }
      })
    });

    // Get Peer List
    this.socketService.peerUserList().subscribe((data: any) => {
      console.log("peerUserList", data);

      if (data?.players?.length) {
        let peerUserList = data.players;
        this.peerUserList = peerUserList;
      }
    })

    // Get Camera Details
    this.socketService.getCamDetails().subscribe((data: any) => {
      console.log("getCamDetails", data);
      console.log("this.senderId", this.senderId);
      console.log("this.peerUserList", this.peerUserList);

      this.peerUserList.forEach((user: any) => {
        if (user.playerNo == data.playerNo && user.peerId) {
          this.socketService.peerIdSend({ peerId: this.senderId, playerNo: 0, publisherSocketId: data.publisherSocketId })
        }
      })
    })

    // Receive PeerId
    this.socketService.peerIdReceived().subscribe((data: any) => {
      console.log("peerIdReceived", data);

      setTimeout(() => {
        this.getUserCam(data.peerId, data.playerNo)
      }, 1000)
    })

    // Reload Page
    this.socketService.reloadPage().subscribe((data: any) => {
      console.log("reloadPage", data);
      window.location.reload();
    })

    // Gesture Set
    this.setGestureSocket()
  }

  getUserClass(playerNo: any) {
    let className = "";

    if (playerNo == 1) {
      className = 'box-6'
      // className = 'd-none'
    } else if (playerNo == 2) {
      className = 'box-7'
    } else if (playerNo == 3) {
      className = 'box-8'
    } else if (playerNo == 4) {
      // className = 'box-9'
      className = 'd-none'
    } else if (playerNo == 5) {
      className = 'd-none'
    } else if (playerNo == 6) {
      // className = 'box-1'
      className = 'd-none'
    } else if (playerNo == 7) {
      className = 'box-2'
    } else if (playerNo == 8) {
      className = 'box-3'
    } else if (playerNo == 9) {
      className = 'box-4'
      // className = 'd-none'
    }

    return className;
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
    }, 1000)
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

  showHandCards(index: any) {
    let show = false;

    if (this.getUserDetails(index + 1)?.sittingOut) {
      show = false
      return
    }

    if (this.getUserDetails(index + 1)?.fold) {
      show = false
    } else if (this.winnerDeclared) {
      show = true
    }

    return show
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



  // Peer Init
  peerInit() {
    navigator.mediaDevices.getUserMedia({
      video: { width: 384, height: 264 },
      audio: false
    }).then((stream) => {
      this.mySteam = stream;
    }).catch((err) => {
      console.log(err);
    });

    this.peer.on("open", (id: any) => {
      this.senderId = id;
      console.log(`this.peer.on("open")`, id)

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
            this.socketService.peerIdSend({ peerId: this.senderId, playerNo: 0, publisherSocketId: user.socketId })
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
      Bet: { start: 3.5, end: 8.5 },
      FakeBetCheck: { start: 8.5, end: 11.5 },
      Disbelief: { start: 11.5, end: 17.0 },
      LookAtCards: { start: 17.0, end: 22.0 },
      Check: { start: 22.0, end: 26.0 },
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
