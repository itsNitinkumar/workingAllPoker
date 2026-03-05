import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// 
import { AuthGuard } from './_guards/auth.guard';

const routes: Routes = [
  {
    path: '',
    children: [
      { path: '', loadChildren: () => import('./public/public.module').then(x => x.PublicModule) },
      { path: 'game-view', loadChildren: () => import('./game-view/game-view.module').then(x => x.GameViewModule), canActivate: [AuthGuard] },
      // { path: 'game', loadChildren: gameViewModule, canActivate: [AuthGuard] },
      // { path: 'pages', loadChildren: pagesModule, canActivate: [AuthGuard] }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class DealerRoutingModule { }
