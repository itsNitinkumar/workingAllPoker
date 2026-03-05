import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// Components Import
import { LayoutComponent } from './layout/layout.component';
import { LobbyComponent } from './lobby/lobby.component';
import { LobComponent } from './lob/lob.component';
import { TableViewComponent } from './table-view/table-view.component';

const routes: Routes = [
  {
    path: '',
    component: LayoutComponent,
    children: [
      { path: 'lobby', component: LobbyComponent },
      { path: 'lob', component: LobComponent },
      { path: 'table-view', component: TableViewComponent },
    ],
  },
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule],
})
export class GameViewRoutingModule {}
