import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';
import { Router, ActivatedRoute } from "@angular/router";

import { NGXToastrService } from '../../shared/services/ngx-toaster.service';
import { AuthenticateService } from '../service/authenticate.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [
    AuthenticateService
  ]
})
export class LoginComponent implements OnInit {

  loginForm: FormGroup;

  constructor(
    private router: Router,
    private spinner: NgxSpinnerService,
    private toastrService: NGXToastrService,
    private authenticateService: AuthenticateService,
    private formBuilder: FormBuilder) {

    this.loginForm = formBuilder.group({
      'email': ['', Validators.compose([Validators.required])],
      'password': ['', Validators.compose([Validators.required])]
    });
  }

  ngOnInit() {
  }

  get loginFormControls() {
    return this.loginForm.controls;
  }

  submitLoginForm() {
    // show spinner
    this.spinner.show();
    // Make sure to create a deep copy of the form-model
    const result = JSON.stringify(this.loginForm.value);
    if (this.loginForm.valid) {
      this.authenticateService.userLogin(result).subscribe((data) => {
        // hide spinner
        this.spinner.hide();
        this.loginForm.reset();
        if (data && data.status_code == 200) {
          // set local storage
          localStorage.setItem('profile_id', data.response_data.profile_id);
          localStorage.setItem('user_role', data.response_data.user_role);
          localStorage.setItem('token', data.response_data.token);
          this.router.navigate(['/user/claim-refund']);

          this.showSuccessMsg(data.response_message);
        } else {
          this.showErrorMsg(data.response_message);
        }
      },
        (err) => {
          // hide spinner
          this.spinner.hide();
          this.loginForm.reset();
          this.showErrorMsg("Login failed.");
        },
        () => {
          // hide spinner
          this.spinner.hide();
        });
    }
  }

  // success toastr
  showSuccessMsg(msg: string) {
    this.toastrService.typeSuccess(msg, "Success");
  }

  // error toastr
  showErrorMsg(msg: string) {
    this.toastrService.typeError(msg, "Error");
  }
}
