import { BrowserModule } from '@angular/platform-browser';
import {  LOCALE_ID, NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { FormsModule } from '@angular/forms';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { NavbarComponent } from './shared/navbar/navbar.component';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { PageNoFoundComponent } from './components/page-no-found/page-no-found.component';
import { IdentityGuard } from './services/identity.guard';
import { UserService } from './services/user.service';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { VideoNewComponent } from './components/video-new/video-new.component';


import { registerLocaleData } from '@angular/common';

    // importar locales
    import localePy from '@angular/common/locales/es-PY';
    import localePt from '@angular/common/locales/pt';
    import localeEn from '@angular/common/locales/en';
import { VideoEditComponent } from './components/video-edit/video-edit.component';
import { VideoDetailComponent } from './components/video-detail/video-detail.component';


    // registrar los locales con el nombre que quieras utilizar a la hora de proveer
    registerLocaleData(localePy, 'es');
    registerLocaleData(localePt, 'pt');
    registerLocaleData(localeEn, 'en')
   

@NgModule({
  declarations: [
    AppComponent,
    NavbarComponent,
    HomeComponent,
    LoginComponent,
    RegisterComponent,
    PageNoFoundComponent,
    UserEditComponent,
    VideoNewComponent,
    VideoEditComponent,
    VideoDetailComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    FormsModule,
    HttpClientModule
  ],
  providers: [
    IdentityGuard,
    UserService,
    { provide: LOCALE_ID, useValue: 'es-Py' }
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
