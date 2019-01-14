import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { RefundClaimComponent } from './refund-claim/refund-claim.component';
import { SearchApplicationComponent } from './search-application/search-application.component';

const routes: Routes = [
    {
        path: '',
        component: RefundClaimComponent
    },
    {
        path: 'claim-refund',
        component: RefundClaimComponent
    },
    {
        path: 'track-status',
        component: SearchApplicationComponent
    }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class UserModuleRoutingModule { }
