import { Component } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  login_form: FormGroup;

  constructor(
    private form_builder: FormBuilder,
    private route: Router
  ) {
    this.login_form = this.form_builder.group({
      user_email: ["", [Validators.required, Validators.email]],
      user_password: ["", [Validators.required]]
    })
  }

  ngOnInit(): void {

  }
  //++++++++++++++++++++++++++++++++++++++++++
  get login_form_controls() {
    return this.login_form.controls;
  }
  //+++++++++++++++++++++++++++++++++++++++++++
  /*== validating credentials ===*/
  validate_credentials() {
    // stop here if form is invalid
    if (this.login_form.invalid) {
      return;
    } else {
    }
    console.log("validate_credentials: ", this.login_form.value)
  }
}
