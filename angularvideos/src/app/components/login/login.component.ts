import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit 
{

  public user: User;
  public error: boolean;

  constructor(private _userSV: UserService,
             private _router: Router)
  {
    this.user = new User('','','','','','','');
  }

  ngOnInit()
  {

  }

  enviarLogin(form: any)
  { 
    this._userSV.signup(this.user).subscribe(
      res=>
      { 
        if(res.status == 'success')
        {
          this.error = false;
          localStorage.setItem('identity', JSON.stringify(res.data));
          this._userSV.signup(this.user, true).subscribe(res=>
            {
              localStorage.setItem('token', res.data);
            })

            setTimeout(() => {
              this._router.navigate(['home']);
            }, 3000);
        }
        else if (res.status == 'error')
        {
          this.error = true;
          //console.log("HAY UN ERROR",res);
        }
        
      },
      err=>{ console.log('Error Login',err);
      }
    );
  }
}
