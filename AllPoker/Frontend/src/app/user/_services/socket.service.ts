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

  // Camera Settings
  public sendCameraControl = (data: any) => {
    this.socket.emit("cameraControlSend", data);

    console.log("cameraControlSend", data);
  }

  public getCameraControl = () => {
    return Observable.create((observer: any) => {
      this.socket.on("cameraControlReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Camera Settings
  public sendCameraSettings = (data: any) => {
    this.socket.emit("cameraSettingsSend", data);
  }

  public getCameraSettings = () => {
    return Observable.create((observer: any) => {
      this.socket.on("cameraSettingsReceive", (data: any) => {
        observer.next(data);
      });
    });
  }


  public getMessages = () => {
    return Observable.create((observer: any) => {
      this.socket.on("getMessage", (message: any) => {
        observer.next(message);
      });
    });
  };

  public connected(message: any) {
    this.socket.emit("connected", message);
  }

  // Poker Gameplay
  // ================================================
  public pokerTableGroupJoin(data: any) {
    this.socket.emit("pokerTableGroupJoin", data);
  }

  public pokerJoinGame(data: any) {
    this.socket.emit("pokerJoinGame", data);
  }

  // Poker Blinds
  public pokerBlindSend(data: any) {
    this.socket.emit("pokerBlindSend", data);
  }

  // Poker Bet
  public pokerBetSend(data: any) {
    this.socket.emit("pokerBetSend", data);
  }

  public pokerBetReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerBetReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Poker Call
  public pokerCallSend(data: any) {
    this.socket.emit("pokerCallSend", data);
  }

  public pokerCallReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerCallReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Poker Check
  public pokerCheckSend(data: any) {
    this.socket.emit("pokerCheckSend", data);
  }

  // Poker Check
  public pokerStraddleSend(data: any) {
    this.socket.emit("pokerStraddleSend", data);
  }

  public pokerCheckReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerCheckReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  public pokerRaiseReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerRaiseReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Poker Fold
  public pokerFoldSend(data: any) {
    this.socket.emit("pokerFoldSend", data);
  }

  // Poker All In
  public pokerAllInSend(data: any) {
    this.socket.emit("pokerAllInSend", data);
  }

  public pokerFoldReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("pokerFoldReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  public sittingOutSet(data: any) {
    this.socket.emit("sittingOutSet", data);
  }

  public timedOutPopupSet(data: any) {
    this.socket.emit("timedOutPopupSet", data);
  }

  public webcamToggleSend(data: any) {
    this.socket.emit("webcamToggleSend", data);
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

  // 
  public cardTableReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("cardTableReceive", (data: any) => {
        observer.next(data);
      });
    });
  }
  public cardPlayerReceive() {
    return Observable.create((observer: any) => {
      this.socket.on("cardPlayerReceive", (data: any) => {
        observer.next(data);
      });
    });
  }
  public mqttComplete() {
    return Observable.create((observer: any) => {
      this.socket.on("mqttComplete", (data: any) => {
        observer.next(data);
      });
    });
  }

  public pokerAudio() {
    return new Observable((observer: any) => {
      this.socket.on("pokerAudio", (data: any) => {
        observer.next(data);
      });
    });
  }

  public pokerTurnPlay() {
    return new Observable((observer: any) => {
      this.socket.on("pokerTurnPlay", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Gesture
  public gestureSend(data: any) {
    this.socket.emit("gestureSend", data);
  }
  public gestureReceive() {
    return new Observable((observer: any) => {
      this.socket.on("gestureReceive", (data: any) => {
        observer.next(data);
      });
    });
  }

  // Dealer
  public dealerGameReset() {
    return new Observable((observer: any) => {
      this.socket.on("dealerGameReset", (data: any) => {
        observer.next(data);
      });
    });
  }


  // Poker Peer Id Send
  public pokerPeerIdSend(data: any) {
    this.socket.emit("pokerPeerIdSend", data);
  }

  public peerUserList() {
    return Observable.create((observer: any) => {
      this.socket.on("peerUserList", (data: any) => {
        observer.next(data);
      });
    });
  }

  public getCamDetails() {
    return Observable.create((observer: any) => {
      this.socket.on("getCamDetails", (data: any) => {
        observer.next(data);
      });
    });
  }

  public peerIdSend(data: any) {
    this.socket.emit("peerIdSend", data);
  }

  public peerIdReceived() {
    return Observable.create((observer: any) => {
      this.socket.on("peerIdReceived", (data: any) => {
        observer.next(data);
      });
    });
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

  // Chat Message Send
  public sendChatMessage = (data: any) => {
    this.socket.emit("sendChatMessage", data);
  }
  public receiveChatMessage() {
    return Observable.create((observer: any) => {
      this.socket.on("receiveChatMessage", (data: any) => {
        observer.next(data);
      });
    });
  }
}
