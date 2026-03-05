import { Component, ViewChild } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { DomSanitizer, SafeResourceUrl, } from '@angular/platform-browser';

import { NgbModal } from '@ng-bootstrap/ng-bootstrap';
import { ToastrService } from 'ngx-toastr';

import { AccountService } from '../../_services/account.service';
import { GameService } from '../../_services/game.service';

@Component({
  selector: 'app-lobby',
  templateUrl: './lobby.component.html',
  styleUrls: ['./lobby.component.css']
})

export class LobbyComponent {
  @ViewChild('content') content: any;

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

  constructor(
    private router: Router,
    private activatedRoute: ActivatedRoute,
    public sanitizer: DomSanitizer,

    private modalService: NgbModal,
    private toastrService: ToastrService,

    private accountService: AccountService,
    private gameService: GameService
  ) { }

  ngOnInit(): void {
    this.table_view_video = document.getElementById('lobby_user_vid');
    this.userData = this.accountService.getUserData();

    this.backgroundUrl = this.sanitizer.bypassSecurityTrustResourceUrl(`https://80.209.238.145:5000/sfu/view.html?id=table_1`);

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
      this.getGameDetails()
    } else {
      this.toastrService.error('No Token Found');
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
          this.toastrService.error(err.error?.message);
        }
      }
    })
  }

  // Open Modal
  modalOpen(seatNo: any, content: any) {
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
      this.toastrService.error('Seat Not Available');
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
}
