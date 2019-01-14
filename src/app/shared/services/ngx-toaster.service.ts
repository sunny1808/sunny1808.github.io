import { Injectable } from '@angular/core';
import { ToastrService } from 'ngx-toastr';

@Injectable({
  providedIn: 'root'
})
export class NGXToastrService {

  constructor(public toastr: ToastrService) { }

  // Success Type
  typeSuccess(successMsg, title) {
    this.toastr.success(successMsg, title, {
      timeOut: 3500
    });
  }

  // Error Type
  typeError(errorMsg, title) {
    this.toastr.error(errorMsg, title, {
      timeOut: 3500
    });
  }
}