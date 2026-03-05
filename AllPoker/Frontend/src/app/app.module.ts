import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { HttpClientModule, HTTP_INTERCEPTORS } from "@angular/common/http";

// Socket  

import { SocketIoModule, SocketIoConfig } from "ngx-socket-io";

import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { ToastrModule, ToastNoAnimationModule } from "ngx-toastr";

// Environment
import { environment } from "src/environments/environment";

// Routing
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';

// Interceptor
import { JwtInterceptor } from "./user/_guards/jwt.interceptor"
import { JwtInterceptorDealer } from "./dealer/_guards/jwt.interceptor"

// Socket Config
const config: SocketIoConfig = {
  url: environment.SOCKET_ENDPOINT,
  options: { path: environment.SOCKET_PATH },
};

const configMediaSoup: SocketIoConfig = {
  url: 'https://80.209.238.145:5000/',
  options: { path: '/mediasoup' },
};

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,

    // Socket Io
    // SocketIoModule.forRoot(configMediaSoup),
    SocketIoModule.forRoot(config),

    // Reactive Forms
    FormsModule,
    ReactiveFormsModule,

    // Bootstrap
    NgbModule,

    // Toastr
    ToastrModule.forRoot({
      timeOut: 10000,
      positionClass: "toast-top-right",
      preventDuplicates: true,
    }),
    ToastNoAnimationModule.forRoot(),
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptor, multi: true },
    { provide: HTTP_INTERCEPTORS, useClass: JwtInterceptorDealer, multi: true },
  ],
  bootstrap: [AppComponent]
})

export class AppModule { }
