import { Component } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  standalone: false,
})
export class HomePage {

  message = '';

  constructor(private api: ApiService) {}

  ionViewWillEnter() {
    this.api.getHello().subscribe((res: any) => {
      this.message = res.message;
    });
  }

}
