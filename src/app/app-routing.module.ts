import { NgModule } from '@angular/core';
import { Routes, RouterModule, PreloadAllModules } from '@angular/router';

import { SidebarLayoutComponent } from './layouts/sidebar-layout/sidebar-layout.component';

import { ALL_ROUTES } from './shared/routes/all-routes.routes';
import { AuthGuard } from './shared/auth/auth-guard.service';

const appRoutes: Routes = [
  {
    path: '',
    redirectTo: 'login',
    pathMatch: 'full',
  },
  {
    path: '',
    component: SidebarLayoutComponent,
    data: {
      title: "Sidebar Layout View"
    },
    children: ALL_ROUTES,
    canActivate: [AuthGuard]
  },
  {
    path: 'login',
    loadChildren: './login/login.module#LoginModule'
  },
  {
    path: 'sign-up',
    loadChildren: './login/login.module#LoginModule'
  }
];

@NgModule({
  imports: [
    RouterModule.forRoot(
      appRoutes/*,
      { enableTracing: true }*/ // <-- debugging purposes only
    )
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
