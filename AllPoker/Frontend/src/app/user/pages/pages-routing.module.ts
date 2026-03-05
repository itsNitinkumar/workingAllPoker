import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';


const tableView = () => import('../../user/pages/table-view/table-view.module').then(x => x.TableViewModule)

const routes: Routes = [
  { path: 'table-view', loadChildren: tableView }
]

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})

export class PagesRoutingModule { }
