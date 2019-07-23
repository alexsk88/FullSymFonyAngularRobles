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

  getVideos(page: any, token: any): Observable<any>
  {
    if(page == null)
    {
      page = 1
    }
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
    .set('Authorization', token);
    return this._http.get(`${this.url}video/list?page=${page}`,{headers});   
  }

  getVideo(id:any, token: any): Observable<any>
  {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
    .set('Authorization', token);
    return this._http.get(`${this.url}video/detail/${id}`,{headers});   
  }


  update(video:any, token: any): Observable<any>
  {
    console.log(video);
    
    let json = JSON.stringify(video);
    let params = 'json='+json;
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
    .set('Authorization', token);
    return this._http.put(`${this.url}video/edit/${video.id}`,params,{headers});   
  }

  delete(id:any, token: any): Observable<any>
  {
    let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
    .set('Authorization', token);
    return this._http.delete(`${this.url}video/delete/${id}`,{headers});   
  }

}
