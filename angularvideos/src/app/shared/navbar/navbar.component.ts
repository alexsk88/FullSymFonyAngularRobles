import { Component, OnInit, DoCheck } from '@angular/core';
import { UserService } from '../../services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit, DoCheck 
{
  public identity: any;
  public token: any;
  
  constructor(private _userSV: UserService,
              private _router: Router)
  {
    
  }

  ngOnInit() 
  {

  }
  
  ngDoCheck()
  {
    this.identity = this._userSV.getIdentity();
    this.token = this._userSV.getToken();
  }

  logout()
  {
    localStorage.removeItem('token');
    localStorage.removeItem('identity');

    this._router.navigate(['home']);
  }

}
