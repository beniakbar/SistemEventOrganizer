import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = '/api'; // Gunakan relative path agar proxy.conf.json bekerja

  constructor(private http: HttpClient) {}

  login(data: { email: string, password: string }) {
    return this.http.post(`${this.apiUrl}/login`, data);
  }
}
