import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';

import { AccountService } from '../_services/account.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-linked-user',
  templateUrl: './linked-user.component.html',
  styleUrls: ['./linked-user.component.css']
})

export class LinkedUserComponent implements OnInit {
  token: any;
  user_token: any;

  constructor(
    private router: Router,
    private activatedRoute: ActivatedRoute,

    private accountService: AccountService,
  ) { }

  ngOnInit(): void {
    this.token = this.activatedRoute.snapshot.queryParams['token'];
    this.user_token = this.activatedRoute.snapshot.queryParams['user_token'];

    if (this.token && this.user_token) {
      this.accountService.checkLinkingToken({ token: this.token, user_token: this.user_token }).subscribe({
        next: (result) => {
          console.log("result", result)

          if (result.status) {
            this.router.navigate(["game/lobby"], { queryParams: { token: this.token } }).then(() => {
              window.location.reload();
            }).catch(() => { });
          } else {
            Swal.fire('Error', result.message, 'error')
          }
        }, error: (err) => {
          console.log("err", err)

          Swal.fire('Error', err.error?.message ? err.error?.message : 'No user found', 'error')
        }
      })
    }

  }

}
