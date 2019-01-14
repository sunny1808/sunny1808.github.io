import { Routes, RouterModule } from '@angular/router';

export const ALL_ROUTES: Routes = [
    {
        path: 'dashboard',
        loadChildren: './dashboard/dashboard.module#DashboardModule'
    },
    {
        path: 'user',
        loadChildren: './user-module/user-module.module#UserModuleModule'
    }
];