import { Component, ViewChild } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeResourceUrl, } from '@angular/platform-browser';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';
import Swal from 'sweetalert2';

import { AccountService } from '../../_services/account.service';
import { GameService } from '../../_services/game.service';

// Game View
import { environment } from 'src/environments/environment';
import { rfidValues } from 'src/rfid_values';
import { SocketService } from '../../_services/socket.service';

@Component({
  selector: 'app-table-view',
  templateUrl: './table-view.component.html',
  styleUrls: ['./table-view.component.css']
})

export class TableViewComponent {
  token: any;
  userData: any;

  // ====================
  // Player Game Details
  // ====================
  userDetails: any;
  userJoined: any = [];
  cardMessage: any = "";

  // Poker Gameplay
  // ===============================================
  gameData: any = {};
  serveCards: any = [];

  socketId: any;

  otherUserIndexes: any = [];
  countDownVal = 60;
  progressBarWidth = 100;

  potValue: any = 0;

  winnerDeclared = false;
  winnerSeatNo = 0;

  gameStarted = false;
  dealerReset = false;
  countDownFn: any;

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
    this.userDetails = this.accountService.getDealerData();
    this.userData = this.accountService.getDealerData();

    if (this.activatedRoute?.snapshot?.queryParams) {
      console.log("activatedRoute.snapshot.queryParams", this.activatedRoute.snapshot.queryParams)
      if (this.activatedRoute.snapshot.queryParams['token']) {
        this.token = this.activatedRoute.snapshot.queryParams['token']
      }
    }

    if (this.token && this.userData) {
      this.getGameDetails()
    } else {
      // this.toastrService.error('No Token Found');
      console.log("this.token", this.token)
      Swal.fire('Error', 'No Token Found', 'error')
    }
  }

  getUserClass(playerNo: any) {
    let className = 'box-1';

    // if (playerNo == 1) {
    //   className = 'd-none';
    //   // className = 'right-top';
    // } else if (playerNo == 2) {
    //   className = 'right-top';
    //   // className = 'right-middle';
    // } else if (playerNo == 3) {
    //   className = 'right-middle';
    //   // className = 'bottom-right';
    // } else if (playerNo == 4) {
    //   className = 'bottom-right';
    //   // className = 'd-none';
    // } else if (playerNo == 5) {
    //   className = 'd-none';
    // } else if (playerNo == 6) {
    //   className = 'bottom-left';
    //   // className = 'd-none';
    // } else if (playerNo == 7) {
    //   className = 'left-middle';
    //   // className = 'bottom-left';
    // } else if (playerNo == 8) {
    //   className = 'left-top';
    //   // className = 'left-middle';
    // } else if (playerNo == 9) {
    //   className = 'd-none';
    //   // className = 'left-top';
    // }

    if (playerNo == 1) {
      className = 'right-top';
    } else if (playerNo == 2) {
      className = 'right-middle';
    } else if (playerNo == 3) {
      className = 'bottom-right';
    } else if (playerNo == 4) {
      className = 'd-none';
    } else if (playerNo == 5) {
      className = 'd-none';
    } else if (playerNo == 6) {
      className = 'd-none';
    } else if (playerNo == 7) {
      className = 'bottom-left';
    } else if (playerNo == 8) {
      className = 'left-middle';
    } else if (playerNo == 9) {
      className = 'left-top';
    }

    return className;
  }

  // Get Game Details
  getGameDetails() {
    this.gameService.gameList({
      token: this.token
    }).subscribe({
      next: (result) => {
        if (result.status) {
          this.pokerSocketSubscribe(result.data.id);
          this.countDownTrigger();
        }
      }
    })
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
      this.gameData = data;
      this.userJoined = data.players;
      this.potValue = data.potValue;
      this.gameStarted = data.gameStarted;
      this.cardMessage = data.cardMessage;
      this.dealerReset = data.dealerReset;

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
      })
    });

    // Reload Page
    this.socketService.reloadPage().subscribe((data: any) => {
      console.log("reloadPage", data);
      window.location.reload();
    })
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

  // Reset Game
  resetGame() {
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, reset it!'
    }).then((result) => {
      if (result.isConfirmed) {
        this.socketService.resetGameSend({
          token: this.token
        })
      }
    })
  }
}
