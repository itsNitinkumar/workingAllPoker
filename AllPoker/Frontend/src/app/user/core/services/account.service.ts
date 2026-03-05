import { Injectable } from '@angular/core';
import { Observable, of, throwError } from 'rxjs';
import { constant_data } from '../constant/constant';

@Injectable({
  providedIn: 'root'
})
export class AccountService {

  constructor() { }

  login(email:any,password:any):Observable<any>{
    /*== checking given email and password is matching with saved user email and password or not ===*/
    if(email == constant_data.login_credentials.email && password == constant_data.login_credentials.password){
      this.setToken();
      return of({"msg":"success"});
    } else {
      return throwError(new Error('Failed to login'));
    }
  }
  //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
  setToken(){
    localStorage.setItem('pockerLogin',btoa('log12345678'))
  }
}
