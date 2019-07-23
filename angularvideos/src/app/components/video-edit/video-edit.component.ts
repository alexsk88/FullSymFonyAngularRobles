import { Component, OnInit } from '@angular/core';
import { Video } from '../../models/video';
import { UserService } from '../../services/user.service';
import { VideoService } from '../../services/video.service';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-video-edit',
  templateUrl: '../video-new/video-new.component.html',
  styleUrls: ['./video-edit.component.css']
})
export class VideoEditComponent implements OnInit {

  public video: Video;
  public identity: any;
  public token : any;
  public status: boolean;
  public page_title: string;
  public btntipo: string;
  public existviideo: boolean
  public msgrta: string

  constructor(private _userSV: UserService,
              private _videoSV: VideoService,
              private _Activedrouter: ActivatedRoute)
  {
    this.identity = _userSV.getIdentity();
    this.token = _userSV.getToken();

    this.page_title = 'Editar Video'
    this.btntipo = 'Actualizar'

    

    this.video = new Video('',this.identity.sub,'','','','','','');
  }

  ngOnInit()
  {
    this.getVideo()
  }

  getVideo()
  {
    let id = +this._Activedrouter.snapshot.paramMap.get("id");

    this._videoSV.getVideo(id, this.token).subscribe(
      res=>
      {
        if(res.status == 'success')
        {
          this.video.title = res.video[0].title
          this.video.id = res.video[0].id
          this.video.description  = res.video[0].description
          this.video.url  = res.video[0].url
          this.existviideo = true;
        }
        else if(res.status == 'error')
        {
          this.existviideo = false;
        }
        //console.log(res);
        
      },err=>console.log(err)
      
    )

  }


  ngSubmit(form:any)
  {
    this._videoSV.update(this.video,this.token).subscribe(
      res=>
      {
        console.log(res);
        this.msgrta = '¡Se ha editado Correctamente!'
        this.status = true;

        setTimeout(() => {
          this.status = false;
        }, 2000);
        
      },err=>{
        console.log(err);
        
      }
    )
  }
}
