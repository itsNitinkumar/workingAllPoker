import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LinkedUserComponent } from './linked-user.component';

describe('LinkedUserComponent', () => {
  let component: LinkedUserComponent;
  let fixture: ComponentFixture<LinkedUserComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [LinkedUserComponent]
    });
    fixture = TestBed.createComponent(LinkedUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
