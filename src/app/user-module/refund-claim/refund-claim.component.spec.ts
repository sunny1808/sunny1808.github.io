import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RefundClaimComponent } from './refund-claim.component';

describe('RefundClaimComponent', () => {
  let component: RefundClaimComponent;
  let fixture: ComponentFixture<RefundClaimComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RefundClaimComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RefundClaimComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
