import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { UserRoutingModule } from './user-routing.module';
import { UserComponent } from './user/user.component';
import { UserHeaderComponent } from './shared/reusableComponents/user-header/user-header.component';
import { UserFooterComponent } from './shared/reusableComponents/user-footer/user-footer.component';
import { LinkedUserComponent } from './linked-user/linked-user.component';


@NgModule({
  declarations: [
    UserComponent,
    UserHeaderComponent,
    UserFooterComponent,
    LinkedUserComponent
  ],
  imports: [
    CommonModule,
    UserRoutingModule
  ]
})
export class UserModule { }
