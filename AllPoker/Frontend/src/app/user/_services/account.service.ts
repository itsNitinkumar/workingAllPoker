import { Injectable } from '@angular/core';
import { Observable, throwError, BehaviorSubject } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';

import { environment } from 'src/environments/environment';
const ApiUrl = environment.apiUrl;

@Injectable({
  providedIn: 'root'
})

export class AccountService {
  constructor(private http: HttpClient, public router: Router) { }

  getToken() { return localStorage.getItem('user_access_token'); }

  getUserData() {
    const user_data = localStorage.getItem('user_data');
    if (user_data) {
      return JSON.parse(user_data);
    } else {
      return null
    }
  }

  get isLoggedIn(): boolean {
    const user = localStorage.getItem('user_access_token');
    if (user == null) {
      return false;
    } else {
      return true;
    }
  }

  login(data: any) {
    return this.http.post<any>(`${ApiUrl}user/account/login`, data).pipe(map(res => {
      if (res.status) {
        localStorage.setItem('user_access_token', res.token);
        localStorage.setItem('user_data', JSON.stringify(res.data));
      }

      return res;
    }));
  }

  checkLinkingToken(data: any) {
    return this.http.post<any>(`${ApiUrl}user/account/check-linking-token`, data).pipe(map(res => {
      if (res.status) {
        localStorage.setItem('user_access_token', res.token);
        localStorage.setItem('user_data', JSON.stringify(res.data));
      }

      return res;
    }));
  }

  logout() {
    localStorage.removeItem('user_access_token');
    localStorage.removeItem('user_data');
    this.router.navigate(['/']);

    window.location.reload();
  }

  profileDetails() {
    return this.http.get<any>(`${ApiUrl}user/account/profile-details`);
  }
}
