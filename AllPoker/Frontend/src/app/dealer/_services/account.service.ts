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

  getToken() { return localStorage.getItem('dealer_access_token'); }

  getDealerData() {
    const dealer_data = localStorage.getItem('dealer_data');
    if (dealer_data) {
      return JSON.parse(dealer_data);
    } else {
      return null
    }
  }

  get isLoggedIn(): boolean {
    const dealer = localStorage.getItem('dealer_access_token');
    if (dealer == null) {
      return false;
    } else {
      return true;
    }
  }

  login(data: any) {
    return this.http.post<any>(`${ApiUrl}dealer/account/login`, data).pipe(map(res => {
      if (res.status) {
        localStorage.setItem('dealer_access_token', res.token);
        localStorage.setItem('dealer_data', JSON.stringify(res.data));
      }

      return res;
    }));
  }

  logout() {
    localStorage.removeItem('dealer_access_token');
    localStorage.removeItem('dealer_data');
    this.router.navigate(['/dealer']);

    window.location.reload();
  }

  profileDetails() {
    return this.http.get<any>(`${ApiUrl}dealer/account/profile-details`);
  }
}
