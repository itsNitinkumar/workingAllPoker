import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { GameEntryComponent } from './game-entry/game-entry.component';

//++++++++++++++++++++++++++++++++++++++++++++++++
const routes: Routes = [
  { path: '', component: GameEntryComponent}
];
//++++++++++++++++++++++++++++++++++++++++++++++++
@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class TableViewRoutingModule { }
