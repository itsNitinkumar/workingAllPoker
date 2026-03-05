import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// Import Components
import { LayoutComponent } from './layout/layout.component';
import { LoginComponent } from './login/login.component';
import { CamtestComponent } from './camtest/camtest.component';
import { Camtest2Component } from './camtest2/camtest2.component';

const routes: Routes = [{
  path: '',
  component: LayoutComponent,
  children: [
    { path: "", component: LoginComponent },
    { path: "camtest1", component: CamtestComponent },
    { path: "camtest2", component: Camtest2Component }
  ]
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class PublicRoutingModule { }
