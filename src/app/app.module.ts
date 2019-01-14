import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { NgxSpinnerModule } from 'ngx-spinner';
import { ToastrModule } from 'ngx-toastr';
import { HttpClientModule } from '@angular/common/http';
import { HttpModule } from '@angular/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import * as $ from 'jquery';

import { AppRoutingModule } from './app-routing.module';
import { SharedModule } from './shared/shared.module'
import { AuthService } from './shared/auth/auth.service';
import { AuthGuard } from './shared/auth/auth-guard.service';;

import { AppComponent } from './app.component';
import { SidebarLayoutComponent } from './layouts/sidebar-layout/sidebar-layout.component';

@NgModule({
  declarations: [
    AppComponent,
    SidebarLayoutComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule, // required animations module
    HttpModule,
    HttpClientModule,
    NgxSpinnerModule,
    ToastrModule.forRoot({
      preventDuplicates: true,
    }), // ToastrModule added
    AppRoutingModule,
    SharedModule
  ],
  providers: [
    AuthService,
    AuthGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
