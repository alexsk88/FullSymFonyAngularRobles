import { Component, OnInit } from '@angular/core';
import { UserService } from '../../services/user.service';
import { VideoService } from '../../services/video.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit 
{
  public identity: any
  public token: any
  public viideos;
  public prev_page;
  public next_page;
  public total_page: any

  constructor(private _userSV: UserService,
    private _videoSV: VideoService,
    private _activeRoute: ActivatedRoute
  ) 
  { 
    this.identity = _userSV.getIdentity()
    this.token = _userSV.getToken()
  }

  ngOnInit()
  {
    //let page = +this._activeRoute.snapshot.paramMap.get("page");

    this._activeRoute.params.subscribe
    (params=>{
      var page = +params['page']

      if(page == null || page == 0)
      {
        page = 1
        this.prev_page = 1;
        this.next_page = 2;
      }
      // console.log(page);
      this.getvideos(page)
    })
    
  }

  getvideos(page: any)
  {
    this._videoSV.getVideos(page, this.token).subscribe(
      res=>
      {
        this.viideos = res.videos;
        console.log(res);
        // http://img.youtube.com/vi/IDENTIFICADOR_DEL_VIDEO/0.jpg

        let number_pages = []
        for (let index = 1; index <= res.total_page; index++) 
        {
          number_pages.push(index)  
        }
        this.total_page = number_pages;


        if(page >= 2)
        {
          this.prev_page = page-1;
        }
        else
        {
          this.prev_page = 1;
        }

        if(page < this.total_page)
        {
          this.next_page = page + 1;
        }
        else{
          this.next_page = res.total_page
        }
  
      },err=>{console.log(err);
      }
    )
  }

  getThimbail(url: String)
  {
    let arr = url.split("=");
    
    return  `http://img.youtube.com/vi/${arr[1]}/0.jpg`;
  }

  eliminarvideo(id)
  {
    this._videoSV.delete(id, this.token).subscribe(
      res=>
      {
        if(res.status == 'success')
        {
          let pageact = this._activeRoute.snapshot.paramMap.get("page");
          this.getvideos(pageact);
        }
        console.log(res);
        
      },err=>{console.log(err);
      }
    )
  }
}
