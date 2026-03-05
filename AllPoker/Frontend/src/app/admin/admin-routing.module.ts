import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

// Import Components
import { AdminComponent } from './admin/admin.component';
import { TestingComponent } from './testing/testing.component';
import { CameraControlComponent } from './camera-control/camera-control.component';

// Import Modules
const publicModule = () => import('./public/public.module').then(x => x.PublicModule);

const routes: Routes = [{
  path: '',
  component: AdminComponent,
  children: [
    { path: '', loadChildren: publicModule },
    { path: 'camera-control', component: CameraControlComponent },
    { path: 'testing', component: TestingComponent }
  ]
}];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class AdminRoutingModule { }
