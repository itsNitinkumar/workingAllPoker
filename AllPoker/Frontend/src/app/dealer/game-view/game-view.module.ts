import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { GameViewRoutingModule } from './game-view-routing.module';
import { TableViewComponent } from './table-view/table-view.component';


@NgModule({
  declarations: [
    TableViewComponent
  ],
  imports: [
    CommonModule,
    GameViewRoutingModule
  ]
})
export class GameViewModule { }
