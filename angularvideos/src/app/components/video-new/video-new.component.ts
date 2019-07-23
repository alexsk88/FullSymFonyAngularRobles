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
  public page_title: string;
  public btntipo: string;
  public msgrta: string;

  existviideo: boolean

  constructor(private _userSV: UserService,
              private _videoSV: VideoService)
  {
    this.identity = _userSV.getIdentity();
    this.token = _userSV.getToken();

    this.page_title = 'Guardar Video Favorito'
    this.btntipo = 'Agregar'
    this.existviideo = true;
    this.video = new Video('',this.identity.sub,'','','','','','');
  }

  ngOnInit()
  {

  }

  ngSubmit(form:any)
  {
    console.log(this.video);
    this._videoSV.saveVideo(this.video, this.token).subscribe(
      res=>
      {
        // console.log(res);
        this.msgrta = '¡El video ha Sido agregado satisfactoriamente!'
        this.status = true;

        setTimeout(() => {
          this.status = false;
        }, 2000);
      },
      err=>{console.log(err);
      }
    )
  }

}
