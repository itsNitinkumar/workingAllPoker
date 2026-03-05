import { Component, OnInit, Inject, AfterViewInit, ElementRef } from '@angular/core';
import { constant_data } from '../../../core/constant/constant';
import { DOCUMENT } from '@angular/common';
import * as AOS from 'aos';
import * as $ from 'jquery';

@Component({
  selector: 'app-game-entry',
  templateUrl: './game-entry.component.html',
  styleUrls: ['./game-entry.component.css']
})
export class GameEntryComponent implements OnInit, AfterViewInit {
  player1_show_seat_availability: boolean = true;
  player2_show_seat_availability: boolean = true;
  player3_show_seat_availability: boolean = true;
  player4_show_seat_availability: boolean = true;
  player5_show_seat_availability: boolean = true;
  player6_show_seat_availability: boolean = true;
  player7_show_seat_availability: boolean = true;

  player1_show_card_amount: boolean = false;
  player2_show_card_amount: boolean = false;
  player3_show_card_amount: boolean = false;
  player4_show_card_amount: boolean = false;
  player5_show_card_amount: boolean = false;
  player6_show_card_amount: boolean = false;
  player7_show_card_amount: boolean = false;

  player1_serve_card: boolean = false;
  player2_serve_card: boolean = false;
  player3_serve_card: boolean = false;
  player4_serve_card: boolean = false;
  player5_serve_card: boolean = false;
  player6_serve_card: boolean = false;
  player7_serve_card: boolean = false;

  player1_active_highlight: boolean = false;
  player2_active_highlight: boolean = false;
  player3_active_highlight: boolean = false;
  player4_active_highlight: boolean = false;
  player5_active_highlight: boolean = false;
  player6_active_highlight: boolean = false;
  player7_active_highlight: boolean = false;

  player1_active_fold: boolean = false;
  player2_active_fold: boolean = false;
  player3_active_fold: boolean = false;
  player4_active_fold: boolean = false;
  player5_active_fold: boolean = false;
  player6_active_fold: boolean = false;
  player7_active_fold: boolean = false;

  first_card_image: string = '';
  second_card_image: string = '';

  serve_card_status: boolean = true;
  highlight_index: number = 0;
  //+++++++++++++++++++++++++++++++++++++++++++++
  /*=== used for showing fullscreen ==*/
  elem: any
  compNativeElement?: HTMLElement;
  //+++++++++++++++++++++++++++++++++++++++++++++
  /*== live video showing related variables ==*/
  table_view_video: any;
  is_live_video_on: boolean = false;
  //+++++++++++++++++++++++++++++++++++++++++++++
  /*=== used for time showing purpose ===*/
  time_left: number = constant_data.player_turn.out_of_time;
  interval: any;
  //++++++++++++++++++++++++++++++++++++++++++++
  /*=== when winner will declared that time this variable will be ===*/
  winner_status: boolean = false;
  //+++++++++++++++++++++++++++++++++++++++++++
  /*== for showing welcome modal ===*/
  show_welcome_modal: boolean = false;
  //++++++++++++++++++++++++++++++++++++++++++++
  self_connection_error: boolean = false;
  //++++++++++++++++++++++++++++++++++++++++++
  other_user_connection_error: boolean = false;

  constructor(private elementRef: ElementRef) {
    this.compNativeElement = this.elementRef.nativeElement;
  }
  ngOnInit(): void {
    /*=== aos used for styling  ===*/
    AOS.init();
    //+++++++++++++++++++++++++++++++++
    /*== load jquery function  ===*/
    // this.add_jquery_functionality();
    //++++++++++++++++++++++++++++++++++
    this.elem = document.documentElement;
    //+++++++++++++++++++++++++++++++++++
    this.table_view_video = document.getElementById('table_view_video');
    //++++++++++++++++++++++++++++++++++++++++
    setTimeout(() => {
      this.show_welcome_modal = true;
    }, 1000);

    // this.showLiveVideo();
  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++
  ngAfterViewInit(): void {
    // this.fullScreen(this.compNativeElement);
  }
  //+++++++++++++++++++++++++++++++++++++++++++++
  fullScreen(element: any) {
    console.log("fullScreen")
    /*=== for run full screen within safari I have used this section code (https://stackblitz.com/edit/safari-fullscreen?file=src%2Fapp%2Fapp.component.ts) ==*/
    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
      /* Firefox */
      element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) {
      /* Chrome, Safari and Opera */
      element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) {
      /* IE/Edge */
      element.msRequestFullscreen();
    }

    /*=== this code will work also but above code will run into safari ===*/
    // if (this.elem.requestFullscreen) {
    //   this.elem.requestFullscreen();
    // } else if (this.elem.mozRequestFullScreen) {
    //   /* Firefox */
    //   this.elem.mozRequestFullScreen();
    // } else if (this.elem.webkitRequestFullscreen) {
    //   /* Chrome, Safari and Opera */
    //   this.elem.webkitRequestFullscreen();
    // } else if (this.elem.msRequestFullscreen) {
    //   /* IE/Edge */
    //   this.elem.msRequestFullscreen();
    // }
  }
  exitFullScreen() {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    }
  }
  /*=== when user will click seat open option that time status will change and amount will be display ===*/
  update_seat_status(player_seat_number: string, enable_option: string) {
    /*=== here checking the updated status option and updating user visibility accordingly ===*/
    if (enable_option == 'show_card_amount') {
      this.show_player_card_amount(player_seat_number);
    }
    // player_position .style.setProperty('display', 'none');
  }
  /*===== if user click on seat open option that time status will change and user can see the amount ===*/
  show_player_card_amount(player_seat_number: string) {
    if (player_seat_number == 'player1') {
      this.player1_show_card_amount = true;
      this.player1_show_seat_availability = false;
    } else if (player_seat_number == 'player2') {
      this.player2_show_card_amount = true;
      this.player2_show_seat_availability = false;
    } else if (player_seat_number == 'player3') {
      this.player3_show_card_amount = true;
      this.player3_show_seat_availability = false;
    } else if (player_seat_number == 'player4') {
      this.player4_show_card_amount = true;
      this.player4_show_seat_availability = false;
    } else if (player_seat_number == 'player5') {
      this.player5_show_card_amount = true;
      this.player5_show_seat_availability = false;
    } else if (player_seat_number == 'player6') {
      this.player6_show_card_amount = true;
      this.player6_show_seat_availability = false;
    } else if (player_seat_number == 'player7') {
      this.player7_show_card_amount = true;
      this.player7_show_seat_availability = false;
    }
  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  /*=== when user will press serve card button that time this function will be executed ===*/
  serve_cards() {
    //++++++++++++++++++++++++++
    /*== when user will press serve cards button that time serve_card_status variable value will true ==*/
    this.update_serve_cards_status(true);
    //++++++++++++++++++++++++++++

    if (this.player1_show_card_amount && !this.player1_show_seat_availability) {
      this.player1_serve_card = true;
    }
    if (this.player2_show_card_amount && !this.player2_show_seat_availability) {
      this.player2_serve_card = true;
    }
    if (this.player3_show_card_amount && !this.player3_show_seat_availability) {
      this.player3_serve_card = true;
    }
    if (this.player4_show_card_amount && !this.player4_show_seat_availability) {
      this.player4_serve_card = true;
    }
    if (this.player5_show_card_amount && !this.player5_show_seat_availability) {
      this.player5_serve_card = true;
    }
    if (this.player6_show_card_amount && !this.player6_show_seat_availability) {
      this.player6_serve_card = true;
    }
    if (this.player7_show_card_amount && !this.player7_show_seat_availability) {
      this.player7_serve_card = true;
    }

    // Deal each card audio
    this.playAudio('deal_each_card');
    this.playAudio('deal_each_card');
  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++++++
  update_serve_cards_status(status: boolean) {
    this.serve_card_status = true;
    //++++++++++++++++++++++++++++++++++++++++
    if (this.serve_card_status) {
      this.activate_player_turn_option(true)
    }
  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++++
  /*=== after player receive cards player turn option will activate which will highlight player amount ==*/
  activate_player_turn_option(is_active: boolean) {
    if (is_active) {
      this.highlight_index = 1;
      this.active_highlight();
    }
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++++
  /*== this function will highlight the amount which will indicate now that user turn ===*/
  active_highlight() {
    /*=== this.highlight_index == 0 means we don't have to highlight any user amount ===*/
    if (this.highlight_index == 0) {
      setTimeout(() => {
        this.player7_active_highlight = false;
        this.stop_time_count();
        //++++++++++++++++++++++++++++++++++++++
        // this.show_time_count();
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 1 || this.highlight_index == 8 || this.highlight_index == 15) {
      /*=== when first player turn on that time we have to make fast,
            for that reson I set 1 second ==*/
      if (this.highlight_index == 1) {
        // this.reset_time_count();
        setTimeout(() => {
          this.other_user_connection_error = false;
          //+++++++++++++++++++++++++++++++++++
          this.player7_active_highlight = false;
          this.player1_active_highlight = true;
          //++++++++++++++++++++++++++++++++++++++
          this.show_time_count();
          //+++++++++++++++++++++++++++++++++++++
          /*=== when the user turn come that time this card images will show below right corner
                now it's static but we have make dynamic these images later ===*/
          this.first_card_image = "assets/images/FrontCardVariants1.png";
          this.second_card_image = "assets/images/FrontCardVariants2.png";
          //++++++++++++++++++++++++++++++++++++++
          this.highlight_index++;
          this.active_highlight();
        }, constant_data.player_turn.first_player_turn_active_time);
      } else {
        setTimeout(() => {
          if (this.highlight_index == 8) {
            this.self_connection_error = true;
          }
          //++++++++++++++++++++++++++++
          this.other_user_connection_error = false;
          //++++++++++++++++++++++++++++
          this.player7_active_highlight = false;
          this.player1_active_highlight = true;
          //++++++++++++++++++++++++++++++++++++++
          this.first_card_image = "assets/images/FrontCardVariants1.png";
          this.second_card_image = "assets/images/FrontCardVariants2.png";
          // this.show_time_count();
          //++++++++++++++++++++++++++++++++++++++
          this.highlight_index++;
          this.active_highlight();
        }, constant_data.player_turn.player_turn_time);
      }
    } else if (this.highlight_index == 2 || this.highlight_index == 9 || this.highlight_index == 16) {
      // this.reset_time_count();
      setTimeout(() => {
        if (this.highlight_index == 9) {
          this.self_connection_error = false;
        }
        //++++++++++++++++++++++++++++++++++++
        this.other_user_connection_error = false;
        //+++++++++++++++++++++++++++++++++++++
        this.player1_active_highlight = false;
        this.player2_active_highlight = true;
        //++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/Front_Card_Variants_2.svg";
        this.second_card_image = "assets/images/Front_Card_Variants_4.svg";
        // this.show_time_count();
        //+++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        this.active_highlight()
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 3 || this.highlight_index == 10 || this.highlight_index == 17) {
      // this.reset_time_count();
      setTimeout(() => {
        this.other_user_connection_error = false;
        //+++++++++++++++++++++++++++++++++++++++
        this.player2_active_highlight = false;
        this.player3_active_highlight = true;
        //++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/FrontCardVariants1.png";
        this.second_card_image = "assets/images/FrontCardVariants2.png";
        // this.show_time_count();
        //+++++++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        this.active_highlight()
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 4 || this.highlight_index == 11 || this.highlight_index == 18) {
      // this.reset_time_count();
      setTimeout(() => {
        //++++++++++++++++++++++++++++++++++++++
        this.other_user_connection_error = false;
        //++++++++++++++++++++++++++++++++++++++
        // this.show_time_count();
        this.player3_active_highlight = false;
        this.player4_active_highlight = true;
        //+++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/Front_Card_Variants_2.svg";
        this.second_card_image = "assets/images/Front_Card_Variants_4.svg";
        //++++++++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        this.active_highlight()
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 5 || this.highlight_index == 12 || this.highlight_index == 19) {
      setTimeout(() => {
        if (this.highlight_index == 12) {
          this.other_user_connection_error = true;
          this.table_view_video.srcObject = null;
        } else {
          this.other_user_connection_error = false;
        }
        this.player4_active_highlight = false;
        this.player5_active_highlight = true;
        //+++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/FrontCardVariants1.png";
        this.second_card_image = "assets/images/FrontCardVariants2.png";
        //+++++++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        this.active_highlight()
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 6 || this.highlight_index == 13 || this.highlight_index == 20) {
      setTimeout(() => {
        if (this.table_view_video.srcObject == null) {
          this.showLiveVideo();
        }
        this.other_user_connection_error = false;
        //+++++++++++++++++++++++++++++++++++++++++++
        this.player5_active_highlight = false;
        this.player6_active_highlight = true;
        //+++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/Front_Card_Variants_2.svg";
        this.second_card_image = "assets/images/Front_Card_Variants_4.svg";
        //++++++++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        this.active_highlight()
      }, constant_data.player_turn.player_turn_time);
    } else if (this.highlight_index == 7 || this.highlight_index == 14 || this.highlight_index == 21) {
      console.log("this.highlight_index: ", this.highlight_index)
      setTimeout(() => {
        this.other_user_connection_error = false;
        //+++++++++++++++++++++++++++++++++++++
        this.player6_active_highlight = false;
        this.player7_active_highlight = true;
        //+++++++++++++++++++++++++++++++++++++++
        this.first_card_image = "assets/images/FrontCardVariants1.png";
        this.second_card_image = "assets/images/FrontCardVariants2.png";
        //+++++++++++++++++++++++++++++++++++++++
        this.highlight_index++;
        if (this.highlight_index >= 21) {
          /*=== stoping the this.active_highlight() execution ===*/
          this.highlight_index = 0;
          this.winner_status = true;
          this.active_highlight()

          // Winner audio
          this.playAudio('push_chips_to_winner');
        } else {
          this.active_highlight()
        }
      }, constant_data.player_turn.player_turn_time);
    }
    //++++++++++++++++++++++++++++++++++++++++++++++++++++++
    // if(this.highlight_index >= 21){
    //   /*=== stoping the this.active_highlight() execution ===*/
    //   this.highlight_index = 0;
    //   this.active_highlight()
    // }
  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++++++++
  fold_cards() {
    if (this.player1_active_highlight) {
      this.player1_active_fold = true;
    } else if (this.player2_active_highlight) {
      this.player2_active_fold = true;
    } else if (this.player3_active_highlight) {
      this.player3_active_fold = true;
    } else if (this.player4_active_highlight) {
      this.player4_active_fold = true;
    } else if (this.player5_active_highlight) {
      this.player5_active_fold = true;
    } else if (this.player6_active_highlight) {
      this.player6_active_fold = true;
    } else if (this.player7_active_highlight) {
      this.player7_active_fold = true;
    }

    // Fold audio
    this.playAudio('fold_cards');
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++
  showLiveVideo() {
    navigator.mediaDevices.getUserMedia({
      video: { width: 384, height: 264 },
      audio: false
    }).then(stream => this.table_view_video.srcObject = stream);
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++
  show_live_streaming() {
    if (!this.is_live_video_on) {
      this.showLiveVideo();
      this.is_live_video_on = true;
    } else {
      console.log("video pause: ")
      // this.table_view_video.pause();
      this.table_view_video.srcObject = null;
      this.is_live_video_on = false;
    }
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++
  show_time_count() {
    this.interval = setInterval(() => {
      if (this.time_left > 0) {
        this.time_left--;
      } else {
        this.time_left = constant_data.player_turn.out_of_time;
        // // this.interval = 60;
        // clearInterval(0);
      }
    }, 1000)
  }
  stop_time_count() {
    clearInterval(this.interval);
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++
  /*== when user will press ok or x button of welcome modal that time we
       have to show full screen,video,hide the modal ==*/
  activate_user_features() {
    this.fullScreen(this.compNativeElement);
    this.show_welcome_modal = false;
    setTimeout(() => {
      this.show_live_streaming()
    }, 400);

  }
  //+++++++++++++++++++++++++++++++++++++++++++++++++++
  add_jquery_functionality() {
    $(document).ready(function () {
      $('#slide').click(function () {
        console.log("coming..")
        var toggle = $('.toggle');
        if (toggle.hasClass('visible')) {
          toggle.animate({ "left": "0px" }, "slow").removeClass('visible');
        } else {
          toggle.animate({ "left": "0px" }, "slow").addClass('visible');
        }
      });
    });
    // $(document).ready(function () {
    //   $('#slide').click(function () {
    //     var hidden = $('.hidden');
    //     if (hidden.hasClass('visible')) {
    //       hidden.animate({ "left": "0px" }, "slow").removeClass('visible');

    //     } else {
    //       hidden.animate({ "left": "-500px" }, "slow").addClass('visible');
    //     }
    //   });
    // });
  }

  // PLAY AUDIO
  playAudio(type: string) {
    let audio = new Audio();

    switch (type) {
      case 'deal_each_card':
        audio.src = "assets/audio/Dealing_each_card.wav";
        break;
      case 'fold_cards':
        audio.src = "assets/audio/Folding_Cards.wav";
        break;
      case 'push_chips_to_winner':
        audio.src = "assets/audio/Dealer_Pushing_chips_to_the_winner.wav";
        break;
    }

    audio.load();
    audio.play();
  }
}
