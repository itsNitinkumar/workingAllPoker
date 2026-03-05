import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CameraControlComponent } from './camera-control.component';

describe('CameraControlComponent', () => {
  let component: CameraControlComponent;
  let fixture: ComponentFixture<CameraControlComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [CameraControlComponent]
    });
    fixture = TestBed.createComponent(CameraControlComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
