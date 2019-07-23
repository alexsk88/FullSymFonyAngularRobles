import { Component, OnInit } from '@angular/core';
import { Video } from '../../models/video';
import { UserService } from '../../services/user.service';
import { VideoService } from '../../services/video.service';

@Component({
  selector: 'app-video-new',
  templateUrl: './video-new.component.html',
  styleUrls: ['./video-new.component.css']
})
export class VideoNewComponent implements OnInit
{
  public video: Video;
  public identity: any;
  public token : any;
  public status: boolean;

  constructor(private _userSV: UserService,
              private _videoSV: VideoService)
  {
    this.identity = _userSV.getIdentity();
    this.token = _userSV.getToken();

    this.video = new Video('',this.identity.sub,'','','','','','');
  }

  ngOnInit()
  {

  }

  enviarNewVideo(form:any)
  {
    console.log(this.video);
    this._videoSV.saveVideo(this.video, this.token).subscribe(
      res=>
      {
        // console.log(res);
        this.status = true;
      },
      err=>{console.log(err);
      }
    )
  }

}
