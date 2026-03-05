import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { PublicRoutingModule } from './public-routing.module';
import { LoginComponent } from './login/login.component';
import { LayoutComponent } from './layout/layout.component';
import { CamtestComponent } from './camtest/camtest.component';
import { Camtest2Component } from './camtest2/camtest2.component';


@NgModule({
  declarations: [
    LoginComponent,
    LayoutComponent,
    CamtestComponent,
    Camtest2Component
  ],
  imports: [
    CommonModule,
    PublicRoutingModule,
    ReactiveFormsModule,
    FormsModule
  ]
})
export class PublicModule { }
