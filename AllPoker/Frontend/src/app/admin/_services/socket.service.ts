import { Injectable } from '@angular/core';
import { Observable, throwError } from 'rxjs';
import { Socket } from "ngx-socket-io";
import { catchError, map } from 'rxjs/operators';
import { HttpClient, HttpHeaders, HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';

import { environment } from 'src/environments/environment';
const ApiUrl = environment.apiUrl;

@Injectable({
  providedIn: 'root'
})
export class SocketService {
  headers = new HttpHeaders().set('Content-Type', 'application/json');

  constructor(
    private http: HttpClient,
    private socket: Socket,
    public router: Router
  ) { }

  public connected(message: any) {
    this.socket.emit("connected", message);
  }

  // Camera Settings
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
}
