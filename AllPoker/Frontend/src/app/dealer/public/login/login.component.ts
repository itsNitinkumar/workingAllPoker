import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from "@angular/router";
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

import { ToastContainerDirective, ToastrService } from "ngx-toastr";
import Swal from 'sweetalert2';

import { Peer } from "peerjs";

import { AccountService } from '../../_services/account.service';
import { SocketService } from '../../_services/socket.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})

export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  token: any;

  isLoggedIn: boolean = false;
  userData: any;

  // peer = new Peer("456");

  constructor(
    private router: Router,
    private activatedRoute: ActivatedRoute,
    private form_builder: FormBuilder,
    private toastrService: ToastrService,

    private accountService: AccountService,
    private socketService: SocketService
  ) {
    this.loginForm = this.form_builder.group({
      username: ["", [Validators.required, Validators.email]],
      password: ["", [Validators.required]]
    })
  }

  ngOnInit(): void {
    this.token = this.activatedRoute.snapshot.queryParams['token'];

    this.isLoggedIn = this.accountService.isLoggedIn;

    if (this.isLoggedIn) {
      this.userData = this.accountService.getDealerData();

      console.log("userData", this.userData)
    }

    // if (isLoggedIn) {
    //   this.router.navigate(["game/lobby"], { queryParams: { token: this.token } }).then(() => {
    //     window.location.reload();
    //   }).catch(() => { });
    // }
  }

  submit() {
    let formVal = this.loginForm.value;
    let val = {
      username: formVal.username,
      password: formVal.password,
    }

    this.accountService.login(val).subscribe({
      next: (result) => {
        if (result.status) {
          // this.toastrService.success(result.message, 'Success');
          if (!this.token) {
            this.token = '1234567890'
          }

          if (result.status) {
            this.router.navigate(["dealer/game-view/table-view"], { queryParams: { token: this.token } }).then(() => {
              window.location.reload();
            }).catch(() => { });
          }
        } else {
          Swal.fire('Error', result.message ? result.message : 'Please try again', 'error')
        }
      },
      error: (error) => {
        Swal.fire('Error', error?.error?.message ? error?.error?.message : 'Please try again', 'error')
      },
    });
  }

  gotoLobby() {
    if (!this.token) {
      this.token = '1234567890'
    }
    this.router.navigate(["dealer/game-view/table-view"], { queryParams: { token: this.token } }).then(() => {
      window.location.reload();
    }).catch(() => { });
  }

  logout() {
    this.accountService.logout();
    this.socketService.reloadPageSend({ userId: this.userData.user_id });
  }

}
