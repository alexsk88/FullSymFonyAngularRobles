import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit
{

  public user: User;
  public status: boolean;

  constructor(private _userSV: UserService)
  {
    this.user = new User('','','','','','ROLE_ADMIN','');
  }

  ngOnInit()
  {


  }

  enviarRegister(form:any)
  {
    console.log(this.user);
    this._userSV.register(this.user).subscribe(
      res=>
      {
        console.log(res);
        this.status = true
      },
      err=>
      {
        console.log(err);
        
      }
    )
  }

}
