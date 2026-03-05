import { Injectable } from '@angular/core';
import { Observable, Observer, throwError } from 'rxjs';
import { Socket } from "ngx-socket-io";
import { catchError, map } from 'rxjs/operators';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';

import { environment } from 'src/environments/environment';
import { data } from 'jquery';
const ApiUrl = environment.apiUrl;

@Injectable({
  providedIn: 'root'
})

export class SocketService {
  headers = new HttpHeaders().set('Content-Type', 'application/json');
  currentUser = {};

  constructor(private http: HttpClient, private socket: Socket, public router: Router) { }

  // Poker Gameplay
  // ================================================
  public pokerTableGroupJoin(data: any) {
    this.socket.emit("pokerTableGroupJoin", data);
  }

  // 
  public getSocketId() {
    return Observable.create((observer: any) => {
      this.socket.on("getSocketId", (data: any) => {
        observer.next(data);
      });
    });
  }

  public pokerGameDetails() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerGameDetails", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Reset Page
  public resetGameSend(data: any) {
    this.socket.emit("resetGameSend", data);
  }

  // Reload Page
  public reloadPageSend(data: any) {
    this.socket.emit("reloadPageSend", data);
  }

  public reloadPage() {
    return Observable.create((observer: any) => {
      this.socket.on("reloadPage", (data: any) => {
        observer.next(data);
      });
    });
  }
}
