import { Component, OnInit } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';

import { NGXToastrService } from '../../shared/services/ngx-toaster.service';
import { AuthenticateService } from '../service/authenticate.service';


@Component({
  selector: 'app-sign-up',
  templateUrl: './sign-up.component.html',
  styleUrls: ['./sign-up.component.css'],
  providers: [
    AuthenticateService
  ]
})
export class SignUpComponent implements OnInit {

  signUpForm: FormGroup;

  constructor(
    private spinner: NgxSpinnerService,
    private toastrService: NGXToastrService,
    private authenticateService: AuthenticateService,
    private formBuilder: FormBuilder) {

    this.signUpForm = formBuilder.group({
      'firstname': ['', Validators.compose([Validators.required])],
      'lastname': ['', Validators.compose([Validators.required])],
      'city': ['', Validators.compose([Validators.required])],
      'state': ['undefined', Validators.compose([Validators.required, this.validateSelectOption])],
      'email': ['', Validators.compose([Validators.required])],
      'password': ['', Validators.compose([Validators.required])],
      'tradename': ['', Validators.compose([Validators.required])]
    });
  }

  ngOnInit() {
  }

  get signUpFormControls() {
    return this.signUpForm.controls;
  }

  submitSignUpForm() {
    // show spinner
    this.spinner.show();
    // Make sure to create a deep copy of the form-model
    const result = JSON.stringify(this.signUpForm.value);
    if (this.signUpForm.valid) {
      this.authenticateService.userSignUp(result).subscribe((responseData) => {
        // hide spinner
        this.spinner.hide();
        this.signUpForm.reset();
        if (responseData && responseData.status_code == 200) {
          this.showSuccessMsg(responseData.response_message);
        } else {
          this.showErrorMsg(responseData.response_message);
        }
      },
        (err) => {
          // hide spinner
          this.spinner.hide();
          this.signUpForm.reset();
          this.showErrorMsg(err.response_message);
        },
        () => {
          // hide spinner
          this.spinner.hide();
        });
    }
  }

  validateSelectOption(input: FormControl) {
    return (input.value !== 'undefined') ? null : { invalidSelectedOption: true };
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
