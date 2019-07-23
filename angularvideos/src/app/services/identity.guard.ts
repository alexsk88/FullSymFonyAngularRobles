import { Injectable } from '@angular/core';
import { CanActivate, Router } from '@angular/router';
import { UserService } from './user.service';

@Injectable()
export class IdentityGuard implements CanActivate {
    
    constructor(private _router: Router,
                private _userSV: UserService) {}

    canActivate()
    {
        let identity = this._userSV.getIdentity();

        if(identity)
        {
            return true;
        }
        else
        {
            this._router.navigate(['/login']);
            return false;
        }
    }
}
