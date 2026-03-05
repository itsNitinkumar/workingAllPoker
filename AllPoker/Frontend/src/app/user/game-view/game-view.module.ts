import { NgModule } from '@angular/core';
import { CommonModule, NgOptimizedImage } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';

import { GameViewRoutingModule } from './game-view-routing.module';
import { LobbyComponent } from './lobby/lobby.component';
import { TableViewComponent } from './table-view/table-view.component';
import { LayoutComponent } from './layout/layout.component';


@NgModule({
  declarations: [
    LobbyComponent,
    TableViewComponent,
    LayoutComponent
  ],
  imports: [
    CommonModule,
    NgOptimizedImage,
    GameViewRoutingModule,
    ReactiveFormsModule,
    FormsModule
  ]
})
export class GameViewModule { }
