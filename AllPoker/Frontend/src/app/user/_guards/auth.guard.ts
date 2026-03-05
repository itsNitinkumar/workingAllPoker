import { Injectable } from '@angular/core';
import {
  ActivatedRouteSnapshot,
  RouterStateSnapshot,
  UrlTree,
  Router,
  ActivatedRoute
} from '@angular/router';
import { Observable } from 'rxjs';

import { AccountService } from '../_services/account.service';

@Injectable({
  providedIn: 'root',
})

export class AuthGuard {
  constructor(
    public accountService: AccountService,
    public router: Router,
    private activatedRoute: ActivatedRoute
  ) { }

  canActivate(
    next: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> | Promise<boolean> | UrlTree | boolean {
    if (this.accountService.isLoggedIn !== true) {
      let token = next.queryParams['token'];

      if (token) {
        this.router.navigate(["/"], { queryParams: { token: token } })
      } else {
        this.router.navigate(['/'], { queryParams: { token: '1234567890' } });
      }
    }
    return true;
  }
}