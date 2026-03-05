import { Component } from '@angular/core';

import { SocketService } from '../_services/socket.service';

@Component({
  selector: 'app-testing',
  templateUrl: './testing.component.html',
  styleUrls: ['./testing.component.css']
})

export class TestingComponent {
  table_view_video: any;
  videoCapabilities: any;
  videoSettings: any;
  videoTrack: any;

  zoomVal = 100;

  deviceId = "default";
  cameraSelect: any;
  videoInputDevices: any = [];
  stream: any;

  constructor(
    private socketService: SocketService
  ) { }

  ngOnInit(): void {
    this.populateCameras();

    this.table_view_video = document.getElementById('lobby_user_vid');

    // this.connectMedia({ video: { pan: true, zoom: 400, tilt: true } })

    this.socketService.getCameraControl().subscribe((data: any) => {
      console.log("getCameraControl", data);

      if (data.type == "zoomIn") {
        this.onRangeChange("zoom", "increase");
      }
      if (data.type == "zoomOut") {
        this.onRangeChange("zoom", "decrease");
      }

      if (data.type == "panRight") {
        this.onRangeChange("pan", "increase");
      }
      if (data.type == "panLeft") {
        this.onRangeChange("pan", "decrease");
      }

      if (data.type == "tiltUp") {
        this.onRangeChange("tilt", "increase");
      }
      if (data.type == "tiltDown") {
        this.onRangeChange("tilt", "decrease");
      }

      // if (data.type == "tiltDown") {
      //   this.onRangeChange("tilt", "increase");
      // }
      // if (data.type == "tiltUp") {
      //   this.onRangeChange("tilt", "decrease");
      // }
    });
  }


  connectMedia(constraints: any) {
    if (this.deviceId != "default" && this.deviceId != "") {
      constraints.video = {
        ...constraints.video,
        ...{ deviceId: { exact: this.deviceId } },
      };
    }

    console.log("constraints", constraints);
    console.log("this.deviceId", this.deviceId);

    navigator.mediaDevices.getUserMedia(constraints).then(async (stream) => {
      this.stream = stream;

      const [videoTrack] = stream.getVideoTracks();
      videoTrack.applyConstraints(constraints);

      console.log(navigator.mediaDevices.getSupportedConstraints());

      this.videoCapabilities = videoTrack.getCapabilities();
      this.videoSettings = videoTrack.getSettings();
      this.videoTrack = videoTrack;

      this.table_view_video.srcObject = stream;

      console.log("this.videoCapabilities", this.videoCapabilities);
      console.log("this.videoSettings", this.videoSettings);
    }).catch((err) => {
      console.log(err);
    })
  }

  onRangeChange(type: any, event: any) {
    const [videoTrack] = this.stream.getVideoTracks();
    const typeCapabilities = this.videoCapabilities ? this.videoCapabilities[type] : null;
    const typeSettings = this.videoSettings ? this.videoSettings[type] : null;

    let constAdv = [{}];
    let valUpdated = false;

    console.log("this.videoCapabilities[type]", this.videoCapabilities[type]);
    console.log("this.videoSettings[type]", this.videoSettings[type]);

    console.log("typeCapabilities", typeCapabilities);
    console.log("typeSettings", typeSettings);

    if (typeCapabilities && typeSettings != undefined) {
      console.log("typeCapabilities && typeSettings")
      constAdv = [];

      const { min, max } = typeCapabilities;
      let { step } = typeCapabilities;

      let val = typeSettings;

      // Zoom & Pan & Tilt
      if (type == "zoom") {
        step = 50
      }
      else if (type == "pan" || type == "tilt") {
        step = max * 20 / 100
      }
      else {
        return
      }

      if (event == 'increase') {
        val += parseFloat(step);
        val = val > max ? max : val
      }

      else if (event == 'decrease') {
        val -= parseFloat(step);
        val = val < min ? min : val
      }

      constAdv.push({ [type]: val.toFixed(1) });

      valUpdated = true;
    } else {
      console.log(`${type} Not Supported`)
    }

    console.log("constAdv", constAdv);

    const constraints = { advanced: constAdv };

    try {
      videoTrack.applyConstraints(constraints).then(() => {
        this.videoSettings = videoTrack.getSettings();

        console.log(this.videoSettings);

        this.socketService.sendCameraSettings({
          videoCapabilities: this.videoCapabilities,
          videoSettings: this.videoSettings
        });
      })

    } catch (error) {
      console.log(error);
    }
  }

  populateCameras() {
    if (!("mediaDevices" in navigator)) return;

    navigator.mediaDevices.enumerateDevices().then((mediaDevices) => {
      let videoInputDevices: MediaDeviceInfo[] = [];

      mediaDevices.forEach((mediaDevice) => {
        if (mediaDevice.kind == "videoinput") {
          videoInputDevices.push(mediaDevice);
        }
      })

      console.log("videoInputDevices", videoInputDevices);
      this.videoInputDevices = videoInputDevices;

      this.connectMedia({ video: { pan: true, zoom: 400, tilt: true } })
    });
  }

  onDeviceChange() {
    console.log("onDeviceChange", this.deviceId);

    if (this.videoTrack) {
      this.videoTrack.stop();
    }

    this.connectMedia({ video: { pan: true, zoom: 400, tilt: true } })
  }

}
