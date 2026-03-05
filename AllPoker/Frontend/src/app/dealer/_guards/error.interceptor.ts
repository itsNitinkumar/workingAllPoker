import { Injectable, Injector } from '@angular/core';
import {
  HttpRequest,
  HttpHandler,
  HttpEvent,
  HttpInterceptor,
  HttpErrorResponse
} from '@angular/common/http';
import { Observable } from 'rxjs';
import { map } from "rxjs/operators";

import { Router } from "@angular/router";
// import {Toaster} from "nw-style-guide/toasts";

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor() { }

  intercept(request: HttpRequest<unknown>, next: HttpHandler): Observable<HttpEvent<unknown>> {
    return next.handle(request);
  }

  // constructor(private _injector: Injector) { }

  // intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
  //   const logFormat = 'background: maroon; color: white';

  //   return next.handle(req).do((event: any) => {
  //   }, (exception: any) => {
  //     if (exception instanceof HttpErrorResponse) {
  //       switch (exception.status) {

  //         case 400:
  //           break;

  //         case 401:
  //           break;

  //         case 404:
  //           break;

  //         case 408:
  //           break;

  //         case 402:
  //           break;

  //         case 500:
  //           break;
  //       }
  //     }
  //   });
  // }
}
