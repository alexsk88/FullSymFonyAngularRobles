import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { api } from './apiglobal';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class VideoService
{

  url = api.url;
  constructor(private _http: HttpClient)
  {

  }

  saveVideo(video: any, token: any): Observable<any>
  {
    let json = JSON.stringify(video);

    let params = 'json='+json;

    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
                                  .set('Authorization', token);

    return this._http.post(`${this.url}video/new`,params,{headers});
  }
}
