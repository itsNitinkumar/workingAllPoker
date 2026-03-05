import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// 
import { AuthGuard } from './_guards/auth.guard';

// Components Import
import { UserComponent } from './user/user.component';
import { LinkedUserComponent } from './linked-user/linked-user.component';

// Modules Import
const publicModule = () => import('../user/public/public.module').then(x => x.PublicModule);
const gameViewModule = () => import('../user/game-view/game-view.module').then(x => x.GameViewModule);
const pagesModule = () => import('../user/pages/pages.module').then(x => x.PagesModule);

const routes: Routes = [
  {
    path: '', component: UserComponent,
    children: [
      { path: '', loadChildren: publicModule },
      { path: 'game', loadChildren: gameViewModule, canActivate: [AuthGuard] },
      { path: 'pages', loadChildren: pagesModule, canActivate: [AuthGuard] },

      { path: 'linkeduser', component: LinkedUserComponent },
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class UserRoutingModule { }
