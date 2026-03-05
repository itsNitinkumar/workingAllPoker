import { Component } from '@angular/core';

import { SocketService } from '../_services/socket.service';

@Component({
  selector: 'app-camera-control',
  templateUrl: './camera-control.component.html',
  styleUrls: ['./camera-control.component.css']
})

export class CameraControlComponent {
  videoInputDeviceList: any;

  constructor(
    private socketService: SocketService
  ) { }

  ngOnInit(): void {
    this.populateCameras();
  }

  populateCameras() {
    if (!("mediaDevices" in navigator)) return;

    navigator.mediaDevices.enumerateDevices().then((mediaDevices) => {
      let videoInputDevices: any = [];

      mediaDevices.forEach((mediaDevice) => {
        if (mediaDevice.kind == "videoinput") {
          videoInputDevices.push({
            deviceDetails: mediaDevice,
            deviceId: mediaDevice ? mediaDevice.deviceId : "default",
            stream: null,
            videoTrack: null,
            videoCapabilities: null,
            videoSettings: null
          });
        }
      })

      this.videoInputDeviceList = videoInputDevices;

      setTimeout(() => {
        this.videoInputDeviceList.forEach((device: any, index: any) => {
          this.connectMedia(device, index);
        })
      }, 1000);
    });
  }

  connectMedia(device: any, index: any) {
    let constraints: any;
    let deviceId = device.deviceId;
    let table_view_video = document.getElementById(`table_view_cam_${index}`);
    let videoInputDeviceList = this.videoInputDeviceList;

    constraints = { video: { pan: true, zoom: 400, tilt: true } };

    if (deviceId != "default" && deviceId != "") {
      constraints.video = {
        ...constraints.video,
        ...{ deviceId: { exact: deviceId } },
      };

      navigator.mediaDevices.getUserMedia(constraints).then(async (stream) => {
        const [videoTrack] = stream.getVideoTracks();
        videoTrack.applyConstraints(constraints);

        let videoCapabilities = videoTrack.getCapabilities();
        let videoSettings = videoTrack.getSettings();

        if (table_view_video) {
          videoInputDeviceList[index].stream = stream;
          videoInputDeviceList[index].videoTrack = videoTrack;
          videoInputDeviceList[index].videoCapabilities = videoCapabilities;
          videoInputDeviceList[index].videoSettings = videoSettings;
        }

        this.videoInputDeviceList = videoInputDeviceList;

        console.log("this.videoInputDeviceList", this.videoInputDeviceList);
        // console.log("this.videoInputDeviceList", this.videoInputDeviceList[index].videoTrack.getCapabilities());

      }).catch((err) => {
        console.log(err);
      })
    }
  }

}
