import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';

import { UserModuleRoutingModule } from './user-module-routing.module';
import { RefundClaimComponent } from './refund-claim/refund-claim.component';
import { SearchApplicationComponent } from './search-application/search-application.component';

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    ReactiveFormsModule,
    UserModuleRoutingModule
  ],
  declarations: [RefundClaimComponent, SearchApplicationComponent]
})
export class UserModuleModule { }
