import { Injectable } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { catchError, map } from 'rxjs/operators';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';

import { environment } from 'src/environments/environment';
const ApiUrl = environment.apiUrl;

@Injectable({
  providedIn: 'root'
})
export class GameService {
  constructor(private http: HttpClient, public router: Router) { }

  findGameOrCreate(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/findGameOrCreate`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameList(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-list`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameChats(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-chats`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameEmojis(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-emojis`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameUserCreate(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-user-create`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameBuyCoin(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-buy-coin`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameUserQuit(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-user-quit`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameUserPeerUpdate(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-peer-update`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }

  gameUserSittingUpdate(data: any) {
    return this.http.post(`${environment.apiUrl}dealer/game/game-sitting-update`, data).pipe(
      map((x: any) => {
        return x;
      })
    );
  }
}
