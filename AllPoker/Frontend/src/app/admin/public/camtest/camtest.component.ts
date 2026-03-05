import { Component } from '@angular/core';

import { Peer } from "peerjs";

@Component({
  selector: 'app-camtest',
  templateUrl: './camtest.component.html',
  styleUrls: ['./camtest.component.css']
})
export class CamtestComponent {
  peer = new Peer("123");

  camtest1_vid: any;
  camtest1_vid2: any;

  ngOnInit(): void {
    this.peer.on("call", (call: any) => {
      // this.peer.call("123", call.peer);
      console.log("demo", call);
      navigator.mediaDevices.getUserMedia({
        video: { width: 384, height: 264 },
        audio: false
      }).then((stream) => {
        console.log("selfCam", stream)
        this.camtest1_vid = document.getElementById('camtest1_vid');
        this.camtest1_vid.srcObject = stream

        // 
        call.answer(stream);
        call.on("stream", (remoteStream: any) => {
          this.camtest1_vid2 = document.getElementById('camtest1_vid2');
          this.camtest1_vid2.srcObject = remoteStream
        });
      }).catch((err) => {
        console.log(err);
      });
    });

  }

  callCam() {
    navigator.mediaDevices.getUserMedia({
      video: { width: 384, height: 264 },
      audio: false
    }).then((stream) => {
      console.log("selfCam", stream)
      this.camtest1_vid = document.getElementById('camtest1_vid');
      this.camtest1_vid.srcObject = stream

      const call = this.peer.call("456", stream);
      call.on("stream", (remoteStream) => {
        this.camtest1_vid2 = document.getElementById('camtest1_vid2');
        this.camtest1_vid2.srcObject = remoteStream
      });
    }).catch((err) => {
      console.log(err);
    });

  }
}
