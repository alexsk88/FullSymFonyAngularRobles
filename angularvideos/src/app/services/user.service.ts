import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { api } from './apiglobal';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class UserService 
{
  url = api.url;

  constructor(private _http: HttpClient)
  {
    this.getIdentity();
  }

  signup(user:any, getToken = null): Observable<any>
  {
    if(getToken)
    {
      user.getToken = true;
    }
    
    let json = JSON.stringify(user);
    let params = 'json='+json;
    // console.log(params);
    
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

    return this._http.post(`${this.url}login`, params,{headers});
  }

  getIdentity()
  {
    let identity = localStorage.getItem('identity');
    
    if(identity != null)
    {
      identity = JSON.parse(identity);
    }
    else
    {
      identity = null;
    }

    return identity;
  }

  getToken()
  {
    return localStorage.getItem('token');
  }
}
