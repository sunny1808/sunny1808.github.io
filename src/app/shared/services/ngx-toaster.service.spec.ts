import { TestBed, inject } from '@angular/core/testing';

import { NgxToasterService } from './ngx-toaster.service';

describe('NgxToasterService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [NgxToasterService]
    });
  });

  it('should be created', inject([NgxToasterService], (service: NgxToasterService) => {
    expect(service).toBeTruthy();
  }));
});
