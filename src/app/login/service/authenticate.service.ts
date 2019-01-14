import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable, of } from 'rxjs';
import { catchError, map, tap } from 'rxjs/operators';

import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class AuthenticateService {
  private loginUrl = "login.php";
  private createUserUrl = "create_user.php";
  private loginUserUrl = "login.php";

  public httpOptions;
  constructor(private http: HttpClient) {
    this.httpOptions = {
      headers: new HttpHeaders({
        'Content-Type': 'application/json'
      })
    };
  }

  userSignUp(postData): Observable<any> {
    return this.http.post(environment.apiUrl + this.createUserUrl, postData);
  }

  userLogin(postData): Observable<any> {
    return this.http.post(environment.apiUrl + this.loginUserUrl, postData);
  }
}