import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from "@angular/router";

import { ROUTES } from './sidebar-routes.config';
import { RouteInfo } from "./sidebar.metadata";

declare var $: any;
@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  public menuItems: any[];
  public admin = false;

  roleObj = {
    admin: {
      dashboard: true,
      claim_refund: false,
      track_status: false
    },
    subscriber: {
      dashboard: false,
      claim_refund: true,
      track_status: true
    }
  }

  constructor(private router: Router,
    private route: ActivatedRoute) {
  }

  ngOnInit() {
    let role = localStorage.getItem("user_role");
    if (role.toLocaleLowerCase() == 'admin') {
      this.admin = true;
    }
    $.getScript('./assets/js/app-sidebar.js');
    //this.menuItems = ROUTES.filter(menuItem => menuItem);
    let data = [];
    this.menuItems = ROUTES.filter(menuItem => {
      // console.log(menuItem)
      let currentRole = localStorage.getItem("user_role").toLocaleLowerCase();
      if (this.roleObj[currentRole].hasOwnProperty(menuItem.role)) {
        if (this.roleObj[currentRole][menuItem.role] == true) {
          let tmpMenuItem = JSON.parse(JSON.stringify(menuItem));
          data.push(tmpMenuItem);
          // this.menuItems.push(tmpMenuItem);
          return tmpMenuItem;
        }
      }
    });
  }

}
