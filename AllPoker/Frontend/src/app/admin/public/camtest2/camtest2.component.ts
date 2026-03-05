import { Component } from '@angular/core';

import { Peer } from "peerjs";

@Component({
  selector: 'app-camtest2',
  templateUrl: './camtest2.component.html',
  styleUrls: ['./camtest2.component.css']
})
export class Camtest2Component {
  peer = new Peer("456");

  camtest2_vid: any;
  camtest2_vid2: any;

  ngOnInit(): void {
    this.peer.on("call", (call: any) => {
      // this.peer.call("123", call.peer);
      console.log("demo", call);
      navigator.mediaDevices.getUserMedia({
        video: { width: 384, height: 264 },
        audio: false
      }).then((stream) => {
        console.log("selfCam", stream)
        this.camtest2_vid = document.getElementById('camtest1_vid');
        this.camtest2_vid.srcObject = stream

        // 
        call.answer(stream);
        call.on("stream", (remoteStream: any) => {
          this.camtest2_vid2 = document.getElementById('camtest2_vid2');
          this.camtest2_vid2.srcObject = remoteStream
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
      this.camtest2_vid = document.getElementById('camtest2_vid');
      this.camtest2_vid.srcObject = stream

      const call = this.peer.call("123", stream);
      call.on("stream", (remoteStream) => {
        this.camtest2_vid2 = document.getElementById('camtest2_vid2');
        this.camtest2_vid2.srcObject = remoteStream
      });
    }).catch((err) => {
      console.log(err);
    });

  }

}
