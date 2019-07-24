import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { VideoService } from '../../services/video.service';
import { DomSanitizer, SafeResourceUrl } from '@angular/platform-browser';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'app-video-detail',
  templateUrl: './video-detail.component.html',
  styleUrls: ['./video-detail.component.css']
})
export class VideoDetailComponent implements OnInit
{
  public video
  public identity: any
  public token: any

  constructor(private activedRoute: ActivatedRoute,
            private _videoSV: VideoService,
            private _sanitizer: DomSanitizer,
            private _userSV: UserService)
  { 
    this.identity = _userSV.getIdentity();
    this.token = _userSV.getToken();
  }

  ngOnInit() 
  {
    this.getVideo()
  }

  getVideo()
  {
    let id = this.activedRoute.snapshot.paramMap.get("id");

    this._videoSV.getVideo(id, this.token).subscribe(
      res=>
      {
        console.log(res);
        if (res.status == 'success')
        {
         console.log(res.video[0]);
         this.video = res.video[0]
         
        }
        
      },err => {console.log(err);
      }
    )
  }

  getVideoIframe(url) {
    var video, results;
 
    if (url === null) {
        return '';
    }
    results = url.match('[\\?&]v=([^&#]*)');
    video   = (results === null) ? url : results[1];
 
    return this._sanitizer
    .bypassSecurityTrustResourceUrl('https://www.youtube.com/embed/' + video);   
}


}
