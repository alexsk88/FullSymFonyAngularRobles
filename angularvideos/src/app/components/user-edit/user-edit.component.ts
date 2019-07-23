import { Component, OnInit } from '@angular/core';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css']
})
export class UserEditComponent implements OnInit
{
  public user: User;
  public status: boolean;
  public token: any;
  public identity: any;

  constructor(private _userSV: UserService)
  {
    this.identity = _userSV.getIdentity();
    this.token = _userSV.getToken();

    this.user = new User('',this.identity.name,this.identity.surname,
    this.identity.email,'','ROLE_ADMIN','');
  }

  ngOnInit()
  {
  }

  enviarEditUser(form:any)
  {
    this._userSV.edituser(this.user, this.token).subscribe(
      res=>
      {
        this.status = true;
        //console.log(res);
        // Aqui SE PUEDE MEJORAR el res.status
        this.identity.name = this.user.name;
        this.identity.surname = this.user.surname;
        this.identity.email = this.user.email;
        localStorage.setItem('identity', JSON.stringify( this.identity))
      },err=>{console.log(err);
      }
    )
  }

}
