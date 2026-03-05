import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Camtest2Component } from './camtest2.component';

describe('Camtest2Component', () => {
  let component: Camtest2Component;
  let fixture: ComponentFixture<Camtest2Component>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [Camtest2Component]
    });
    fixture = TestBed.createComponent(Camtest2Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
