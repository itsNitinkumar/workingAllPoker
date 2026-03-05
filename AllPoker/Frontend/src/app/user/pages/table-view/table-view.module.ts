import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { TableViewRoutingModule } from './table-view-routing.module';
import { GameEntryComponent } from './game-entry/game-entry.component';


@NgModule({
  declarations: [
    GameEntryComponent
  ],
  imports: [
    CommonModule,
    TableViewRoutingModule
  ]
})
export class TableViewModule { }
