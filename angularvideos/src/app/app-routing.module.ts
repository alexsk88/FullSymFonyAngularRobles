import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { PageNoFoundComponent } from './components/page-no-found/page-no-found.component';

import { IdentityGuard } from './services/identity.guard';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { VideoNewComponent } from './components/video-new/video-new.component';
import { VideoEditComponent } from './components/video-edit/video-edit.component';


const routes: Routes = [
  {path:'', component: HomeComponent },
  {path:'home', component: HomeComponent },
  {path:'home/:page', component: HomeComponent,canActivate:[IdentityGuard] },
  {path:'login', component: LoginComponent },
  {path:'registro', component: RegisterComponent },
  {path:'editar-video/:id', component: VideoEditComponent, canActivate:[IdentityGuard] },
  {path:'ajustes', component: UserEditComponent, canActivate:[IdentityGuard] },
  {path:'guardar-favorito', component: VideoNewComponent, canActivate:[IdentityGuard] },
  {path:'**', component: PageNoFoundComponent }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
