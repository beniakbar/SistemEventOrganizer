import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { IonicModule } from '@ionic/angular';
import { Router } from '@angular/router';

@Component({
  selector: 'app-events',
  standalone: true,
  imports: [CommonModule, IonicModule],
  templateUrl: './events.page.html',
  styleUrls: ['./events.page.scss']
})
export class EventsPage implements OnInit {
  events: any[] = [];

  constructor(private http: HttpClient, private router: Router) {}

  ngOnInit() {
    const token = localStorage.getItem('token');
    console.log('Token:', token); // <--- log token
    const headers = new HttpHeaders().set('Authorization', `Bearer ${token}`);
  
    this.http.get('/api/events', { headers }).subscribe({
      next: (res: any) => {
        console.log('Event Data:', res); // <--- log data
        this.events = res;
      },
      error: err => {
        console.error('API Error:', err); // <--- log error
        alert('Gagal mengambil event.');
      }
    });
  }  

  lihatDetail(id: number) {
    this.router.navigate(['/events', id]);
  }
}
