import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

const userModule = () => import('./user/user.module').then(x => x.UserModule);
const AdminModule = () => import('./admin/admin.module').then(x => x.AdminModule);
const DealerModule = () => import('./dealer/dealer.module').then(x => x.DealerModule);

const routes: Routes = [
  { path: '', loadChildren: userModule },
  { path: 'admin', loadChildren: AdminModule },
  { path: 'dealer', loadChildren: DealerModule },
    { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
