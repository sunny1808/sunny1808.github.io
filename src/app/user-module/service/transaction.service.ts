import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';

import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class TransactionService {
  private refundClaimUrl = "refund.php";

  public httpOptions;
  constructor(private http: HttpClient) {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
  }

  userClaimRefund(postData): Observable<any> {
    return this.http.post(environment.apiUrl + this.refundClaimUrl, postData);
  }
}
