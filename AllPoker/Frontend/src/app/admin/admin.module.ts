import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { AdminRoutingModule } from './admin-routing.module';
import { AdminComponent } from './admin/admin.component';
import { TestingComponent } from './testing/testing.component';
import { CameraControlComponent } from './camera-control/camera-control.component';


@NgModule({
  declarations: [
    AdminComponent,
    TestingComponent,
    CameraControlComponent
  ],
  imports: [
    CommonModule,
    AdminRoutingModule,
    ReactiveFormsModule,
    FormsModule
  ]
})
export class AdminModule { }
