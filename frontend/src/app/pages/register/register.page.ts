import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonicModule } from '@ionic/angular';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [CommonModule, FormsModule, IonicModule],
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss']
})
export class RegisterPage {
  name = '';
  email = '';
  password = '';
  role = 'peserta';

  constructor(private http: HttpClient, private router: Router) {}

  doRegister() {
    const body = {
      name: this.name,
      email: this.email,
      password: this.password,
      role: this.role
    };

    this.http.post('/api/register', body).subscribe({
      next: () => {
        alert('Register berhasil!');
        this.router.navigateByUrl('/login');
      },
      error: () => {
        alert('Gagal register.');
      }
    });
  }
}
