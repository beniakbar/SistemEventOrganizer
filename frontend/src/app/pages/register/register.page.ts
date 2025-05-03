import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router, RouterModule } from '@angular/router';
import { IonicModule, AlertController } from '@ionic/angular';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-register',
  standalone: true,
  // Tambahkan RouterModule ke imports!
  imports: [IonicModule, CommonModule, FormsModule, RouterModule],
  templateUrl: './register.page.html',
  styleUrls: ['./register.page.scss']
})
export class RegisterPage {
  name = '';
  email = '';
  password = '';

  constructor(
    private http: HttpClient,
    private router: Router,
    private alertController: AlertController
  ) {}

  async showAlert(message: string) {
    const alert = await this.alertController.create({
      header: 'Gagal Registrasi',
      message,
      buttons: ['OK'],
    });
    await alert.present();
  }

  register() {
    if (this.password.length < 6) {
      this.showAlert('Password minimal 6 karakter');
      return;
    }

    this.http.post('http://127.0.0.1:8000/api/register', {
      name: this.name,
      email: this.email,
      password: this.password,
    }).subscribe({
      next: () => this.router.navigateByUrl('/login'),
      error: (err) => {
        const msg = err.error?.message || 'Terjadi kesalahan saat registrasi';
        this.showAlert(msg);
      }
    });
  }
}
