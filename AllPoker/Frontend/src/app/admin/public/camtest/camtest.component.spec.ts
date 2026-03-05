import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CamtestComponent } from './camtest.component';

describe('CamtestComponent', () => {
  let component: CamtestComponent;
  let fixture: ComponentFixture<CamtestComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [CamtestComponent]
    });
    fixture = TestBed.createComponent(CamtestComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
