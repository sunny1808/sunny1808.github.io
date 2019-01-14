import { Component, OnInit, ElementRef, ViewChild } from '@angular/core';
import { NgxSpinnerService } from 'ngx-spinner';
import { FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';

import { NGXToastrService } from '../../shared/services/ngx-toaster.service';
import { TransactionService } from '../service/transaction.service';

declare var $: any;

@Component({
  selector: 'app-refund-claim',
  templateUrl: './refund-claim.component.html',
  styleUrls: ['./refund-claim.component.css'],
  providers: [
    TransactionService
  ]
})
export class RefundClaimComponent implements OnInit {

  refundClaimForm: FormGroup;
  formARN;
  refundClaimFormSubmitted = false;

  @ViewChild('fileInput') fileInput: ElementRef;

  constructor(
    private spinner: NgxSpinnerService,
    private toastrService: NGXToastrService,
    private transactionService: TransactionService,
    private formBuilder: FormBuilder) {

    this.refundClaimForm = formBuilder.group({
      'refundType': ['Refund of excess balance in electronic cash'],
      'refundAmount': ['', Validators.compose([Validators.required])],
      'bankAccNumber': ['', Validators.compose([Validators.required])],
      'fileToUpload': null,
      'declaration_status': ['', Validators.compose([Validators.required])],
      'profile_id': ['', Validators.compose([Validators.required])],
      'token': null
    });
  }

  ngOnInit() {
  }

  get refundClaimFormControls() {
    return this.refundClaimForm.controls;
  }

  submitRefundClaimForm() {
    // show spinner
    this.spinner.show();
    // Make sure to create a deep copy of the form-model
    const result = this.prepareSave();

    if (this.refundClaimForm.valid) {
      this.transactionService.userClaimRefund(result).subscribe((data) => {
        // hide spinner
        this.spinner.hide();
        this.refundClaimForm.reset();
        this.clearFile();
        if (data && data.status_code == 200) {
          this.refundClaimFormSubmitted = true;
          this.formARN = data.response_data;
          this.scrollToTop();
          this.showSuccessMsg(data.response_message);
        } else {
          this.refundClaimFormSubmitted = false;
          this.showErrorMsg(data.response_message);
        }
      },
        (err) => {
          // hide spinner
          this.spinner.hide();
          this.refundClaimFormSubmitted = false;
          this.refundClaimForm.reset();
          this.clearFile();
          this.showErrorMsg(err.response_message);
        },
        () => {
          // hide spinner
          this.spinner.hide();
        });
    }
  }

  scrollToTop() {
    $("html, body").animate({ scrollTop: 0 }, "slow");
    return;
  }

  onFileChange(event) {
    if (event.target.files.length > 0) {
      let file = event.target.files[0];
      this.refundClaimForm.get('fileToUpload').setValue(file);

      this.refundClaimForm.get('profile_id').setValue(localStorage.getItem('profile_id'));
    }
  }

  private prepareSave(): any {
    let input = new FormData();
    input.append('refundType', this.refundClaimForm.get('refundType').value);
    input.append('refundAmount', this.refundClaimForm.get('refundAmount').value);
    input.append('bankAccNumber', this.refundClaimForm.get('bankAccNumber').value);
    input.append('fileToUpload', this.refundClaimForm.get('fileToUpload').value);
    input.append('declaration_status', this.refundClaimForm.get('declaration_status').value);
    input.append('profile_id', this.refundClaimForm.get('profile_id').value);
    input.append('Authorization', localStorage.getItem('token'));
    return input;
  }

  // success toastr
  showSuccessMsg(msg: string) {
    this.toastrService.typeSuccess(msg, "Success");
  }

  // error toastr
  showErrorMsg(msg: string) {
    this.toastrService.typeError(msg, "Error");
  }

  clearFile() {
    this.refundClaimForm.get('fileToUpload').setValue(null);
    this.fileInput.nativeElement.value = '';
  }
}
