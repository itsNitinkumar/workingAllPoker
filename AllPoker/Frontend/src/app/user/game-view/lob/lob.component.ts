import { AfterViewInit, Component, ElementRef, Renderer2, ViewEncapsulation } from '@angular/core';
// top of the file (outside the component) — declare the global helper type
interface OverlayHelpers {
  makeOverlay(video: HTMLVideoElement): void;
  removeOverlayFor(video: HTMLVideoElement): void;
  repositionAllOverlays(): void;
  showEmptySeatForId(videoId: string): void;
  clearEmptySeatForId(videoId: string): void;
}

declare global {
  interface Window {
    __overlayHelpers__?: OverlayHelpers;
  }
}

@Component({
  selector: 'app-lob',
  templateUrl: './lob.component.html',
  styleUrls: [
    './lob.component.css',
    '../../../../assets/css/style.css',
    '../../../../assets/css/responsive.css',
  ],
  encapsulation: ViewEncapsulation.None,
})

export class LobComponent implements AfterViewInit {
  constructor(private host: ElementRef, private renderer: Renderer2) {}

  ngAfterViewInit(): void {
    const root = this.host.nativeElement as HTMLElement;

    // --- Asset path utilities to fix dynamic JS-created paths ---
    const mapAsset = (p?: any): any => {
      if (!p || typeof p !== 'string') return p;
      if (
        p.startsWith('http://') ||
        p.startsWith('https://') ||
        p.startsWith('data:')
      )
        return p;
      // normalize leading ./ and ../
      let path = p;
      while (path.startsWith('./')) path = path.slice(2);
      while (path.startsWith('../')) path = path.slice(3);
      const roots = [
        'images/',
        'image/',
        'img/',
        'video/',
        '/images/',
        '/image/',
        '/img/',
        '/video/',
      ];
      const matched = roots.find((r) => path.startsWith(r));
      if (matched) {
        return '/assets/new/' + path.replace(/^\/+/, '');
      }
      return path;
    };

    const rewriteElAttr = (el: Element | null, attr: string) => {
      if (!el) return;
      const v = (el as any).getAttribute?.(attr);
      if (!v) return;
      const mapped = mapAsset(v);
      if (mapped !== v) (el as any).setAttribute?.(attr, mapped);
    };

    const rewriteAllAssets = () => {
      const nodes = root.querySelectorAll(
        '[src], [href], [poster], [data-src]'
      );
      nodes.forEach((n) => {
        rewriteElAttr(n, 'src');
        rewriteElAttr(n, 'href');
        rewriteElAttr(n, 'poster');
        rewriteElAttr(n, 'data-src');
      });
    };

    // Observe dynamic mutations and fix any new asset URLs
    const mo = new MutationObserver((muts) => {
      muts.forEach((m) => {
        if (m.type === 'attributes' && m.target instanceof Element) {
          const attr = m.attributeName || '';
          if (['src', 'href', 'poster', 'data-src'].includes(attr)) {
            rewriteElAttr(m.target as Element, attr);
          }
        } else if (m.type === 'childList') {
          m.addedNodes.forEach((n) => {
            if (n instanceof Element) {
              rewriteAllAssets();
            }
          });
        }
      });
    });
    mo.observe(root, {
      attributes: true,
      childList: true,
      subtree: true,
      attributeFilter: ['src', 'href', 'poster', 'data-src'],
    });

    // Patch HTMLImageElement.src setter so programmatic sets get mapped
    try {
      const imgDesc = Object.getOwnPropertyDescriptor(
        (window as any).HTMLImageElement.prototype,
        'src'
      );
      if (imgDesc && imgDesc.set) {
        Object.defineProperty(
          (window as any).HTMLImageElement.prototype,
          'src',
          {
            set: function (v: any) {
              imgDesc.set!.call(this, mapAsset(v));
            },
            get: function () {
              return imgDesc.get!.call(this);
            },
          }
        );
      }
    } catch {}

    // Initial sweep
    rewriteAllAssets();

    const addClick = (
      selector: string,
      handler: (el: Element, ev: Event) => void
    ) => {
      const nodes = root.querySelectorAll(selector);
      if (!nodes || nodes.length === 0) {
        return;
      }
      nodes.forEach((node) => {
        this.renderer.listen(node, 'click', (ev: Event) => handler(node, ev));
      });
    };

    const toggleClass = (el: Element | null, cls: string) => {
      if (!el) {
        return;
      }
      const has = el.classList.contains(cls);
      if (has) {
        this.renderer.removeClass(el, cls);
      } else {
        this.renderer.addClass(el, cls);
      }
    };

    const addClass = (el: Element | null, cls: string) => {
      if (el) this.renderer.addClass(el, cls);
    };
    const removeClass = (el: Element | null, cls: string) => {
      if (el) this.renderer.removeClass(el, cls);
    };

    // === Ported interactions from custom.js (null-safe) ===

    // Sidebar toggle
    addClick('#menu_bar', () => {
      const leftMenu = root.querySelector('.leftMenu');
      if (leftMenu) toggleClass(leftMenu, 'side-nav');
    });

    addClick('.mobile-menu', () => {
      const blur = root.querySelector('.bg-blur');
      if (blur) removeClass(blur, 'nav-show');
    });

    addClick('#menu-btn', () => {
      const blur = root.querySelector('.bg-blur');
      if (blur) toggleClass(blur, 'nav-show');
    });

    addClick('.smily, .buy-cross4, .hurt', () => {
      const emojiMain = root.querySelector('.emoji-main');
      if (emojiMain) {
        // Using style display toggling similar to slideToggle without animation
        toggleClass(emojiMain, 'd-block');
      }
      const emojiInner = root.querySelector('.emoji-main-inner');
      if (emojiInner) toggleClass(emojiInner, 'add-after');
      if (emojiInner) toggleClass(emojiInner, 'd-block');
    });

    // Tooltip init (only if Bootstrap available)
    const w = window as any;
    if (w && w.bootstrap && w.bootstrap.Tooltip) {
      const tooltipTriggers = Array.from(
        root.querySelectorAll('[data-bs-toggle="tooltip"]')
      );
      tooltipTriggers.forEach((el: any) => {
        try {
          // eslint-disable-next-line new-cap
          new w.bootstrap.Tooltip(el);
        } catch {}
      });
    }

    // Filter panels
    addClick('#filter-icon', (el) => {
      const inner = root.querySelector('.check-main-inner');
      if (inner) toggleClass(inner, 'd-block');
      toggleClass(el, 'fill-bg');
    });
    addClick('#filter-icon2', (el) => {
      const inner = root.querySelector('.check-main-inner');
      if (inner) toggleClass(inner, 'd-block');
      toggleClass(el, 'fill-bg');
    });

    // .item-main click → sync "test" class with .filter-list items by index
    addClick('.item-main', (el) => {
      root
        .querySelectorAll('.filter-list ul li a')
        .forEach((a) => removeClass(a, 'test'));
      toggleClass(el, 'test');
      const parentLi = el.parentElement;
      if (parentLi) {
        const index = Array.from(
          parentLi.parentElement?.children || []
        ).indexOf(parentLi);
        const partners = root.querySelectorAll('.filter-list ul li');
        const partner = partners.item(index)?.querySelector('a');
        if (partner) toggleClass(partner, 'test');
      }
    });

    // .drop-item2 → sync with .filter2
    addClick('.drop-item2', (el) => {
      root
        .querySelectorAll('.filter2 ul li a')
        .forEach((a) => removeClass(a, 'test'));
      toggleClass(el, 'test');
      const parentLi = el.parentElement;
      if (parentLi) {
        const index = Array.from(
          parentLi.parentElement?.children || []
        ).indexOf(parentLi);
        const partners = root.querySelectorAll('.filter2 ul li');
        const partner = partners.item(index)?.querySelector('a');
        if (partner) toggleClass(partner, 'test');
      }
    });

    // Many show/hide toggles grouped:
    addClick('#cross', () => {
      removeClass(root.querySelector('.hand-rank'), 'box-show');
    });
    addClick('.gif-show li a', () => {
      toggleClass(root.querySelector('.puke'), 'puke-show');
    });
    addClick('.emojis-list li a', () => {
      toggleClass(root.querySelector('.react-img'), 'd-block');
      removeClass(root.querySelector('.react-emoji-main'), 'd-block');
    });
    addClick('.quesion', () => {
      addClass(root.querySelector('.hand-main-inner'), 'add-before');
      addClass(root.querySelector('.buy-hero'), 'z-ind2');
      addClass(root.querySelector('.buy-cross6'), 'cross-show');
      toggleClass(root.querySelector('.hand-rank-mbl'), 'box-show');
      addClass(root.querySelector('.fold-main'), 'fold-main-hide');
      addClass(root.querySelector('.cards-main-inner'), 'fold-main-hide');
    });
    addClick('.buy-cross6', () => {
      addClass(root.querySelector('.hand-main-inner'), 'remove-before2');
      addClass(root.querySelector('.camera-setting-main2'), 'test');
      addClass(root.querySelector('.buy-cross6'), 'cross-hide');
      addClass(root.querySelector('.header-2'), 'z-ind3');
    });
    addClick('.switch-red', (el) => {
      toggleClass(el, 'switch-red-btn');
    });
    addClick('.switch-sky', (el) => {
      toggleClass(el, 'switch-sky-btn');
    });
    addClick('.react-loby', () => {
      toggleClass(root.querySelector('.react-emoji-main'), 'd-block');
    });
    addClick('.buy-btn, .fund-cross', () => {
      toggleClass(root.querySelector('.enter-buy-main'), 'd-block');
    });
    addClick('.fold-btn, .buy-cross3', () => {
      toggleClass(root.querySelector('.camera-setting-main2'), 'd-block');
    });
    addClick(
      '.fold-btn, .buy-cross3, .buy-btn, .fund-cross, .time-popup, .time-cross, .leave-cross',
      () => {
        toggleClass(root.querySelector('.buy-hero'), 'z-up');
      }
    );
    addClick('.raise-btn, .deposit-cross', () => {
      toggleClass(root.querySelector('.enter-buy-main2'), 'd-block');
    });
    addClick('.buy-cross7, .quesion', () => {
      toggleClass(root.querySelector('.hand-rank-mbl'), 'box-show1');
      toggleClass(root.querySelector('.hand-rank-mbl'), 'd-flex');
    });
    addClick('#cross, .quesion2', () => {
      toggleClass(root.querySelector('.hand-rank-desktop'), 'd-block');
    });
    addClick('.leave-table, .leave-cross', () => {
      toggleClass(root.querySelector('.time-main'), 'd-block');
    });
    addClick('.time-popup, .time-cross, .leave-table', () => {
      toggleClass(root.querySelector('.time-main-2'), 'd-block');
    });
    addClick('.video-icon', (el) => {
      toggleClass(root.querySelector('.react-video'), 'd-block');
      toggleClass(root.querySelector('.react-video'), 'opacity-100');
      toggleClass(root.querySelector('.loby-avatar'), 'd-none');
      toggleClass(el, 'icon-bg');
    });
    addClick('.message, .video-icon, .support, .mute-sound, .smily', (el) => {
      toggleClass(el, 'icon-bg');
    });
    addClick('.hurt', () => {
      toggleClass(root.querySelector('.my-react-img'), 'show');
    });

    // jQuery UI Sliders — initialize only if jQuery + slider present; else, fallback to just set initial text values.
    const jq = (window as any)['jQuery'] || (window as any)['$'];
    const initSlider = (
      id: string,
      max: number,
      value: number,
      labelId: string,
      prefix = '$'
    ) => {
      const slider = root.querySelector(id) as any;
      const label = root.querySelector(labelId);
      if (!slider || !label) {
        return;
      }

      if (jq && jq.fn && jq.fn.slider) {
        try {
          jq(id, root).slider({
            range: 'min',
            min: 0,
            max,
            value,
            slide: (event: any, ui: any) => {
              if (label) label.textContent = `${prefix}${ui.value}`;
            },
          });
          if (label)
            label.textContent = `${prefix}${jq(id, root).slider('value')}`;
        } catch {
          // fallback
          if (label) label.textContent = `${prefix}${value}`;
        }
      } else {
        // Fallback: set initial value only
        label.textContent = `${prefix}${value}`;
      }
    };

    initSlider('#price-range', 1000, 500, '#min-price');
    initSlider('#price-range2', 7550, 2550, '#min-price2');
    initSlider('#price-range3', 2000, 550, '#min-price3');
    initSlider('#price-range4', 2000, 550, '#min-price4');
    initSlider('#price-range5', 2000, 550, '#min-price5');
    
     (function () {
    const vc = document.getElementById('videoContainer');
    if (!vc) {
      console.warn('videoContainer not found!');
      return;
    }

    function positionOverlayFor(video: HTMLElement, box: HTMLElement, vcRect: DOMRect): void {
      const r = video.getBoundingClientRect();
      box.style.left = (r.left - vcRect.left) + 'px';
      box.style.top = (r.top - vcRect.top) + 'px';
      box.style.width = r.width + 'px';
      box.style.height = r.height + 'px';
    }

    function parseCharSeat(videoId: string): { ch: string; seat: string } | null {
      const m = /video_character(\d+)_seat(\d+)/.exec(videoId);
      return m ? { ch: m[1], seat: m[2] } : null;
    }

    function ensureOverlayBox(videoId: string): HTMLElement {
      const id = 'overlay_' + videoId;
      let box = document.getElementById(id) as HTMLElement | null;
      if (!box) {
        box = document.createElement('div');
        box.id = id;
        box.className = 'overlay-box';
        box.innerHTML = `
          <div class="text-box justify-content-start buy-btn">
            <h5 class="call-main"><span class="call">B</span>$100</h5>
          </div>`;

        (vc as HTMLElement).appendChild(box);

      }
      return box;
    }

    function setEmptySeatImg(box: HTMLElement, ch: string, seat: string): void {
      let img = box.querySelector('img.empty-seat') as HTMLImageElement | null;
      if (!img) {
        img = document.createElement('img') as HTMLImageElement;
        img.className = 'empty-seat';
        img.alt = 'Empty seat';
        // inline styling (explicit assignments to keep typing)
        img.style.position = 'absolute';
        img.style.inset = '0';
        img.style.width = '100%';
        img.style.height = '100%';
        // objectFit and pointerEvents are string properties on CSSStyleDeclaration
        (img.style as any).objectFit = 'contain';
        (img.style as any).pointerEvents = 'none';
        img.style.filter = 'drop-shadow(0 2px 6px rgba(0,0,0,.35))';
        img.style.display = 'block';
        box.appendChild(img);
      }
      img.src = `video/character${ch}-seat${seat}.png`;
      img.style.display = 'block';
      box.dataset['empty'] = '1';
    }

    function clearEmptySeatImg(box: HTMLElement): void {
      const img = box.querySelector('img.empty-seat') as HTMLImageElement | null;
      if (img) img.style.display = 'none';
      delete box.dataset['empty'];
    }

    // Build the strongly typed helpers object
    const helpers: OverlayHelpers = {
      makeOverlay(video: HTMLVideoElement): void {
        const box = ensureOverlayBox(video.id);
        clearEmptySeatImg(box);
        const vcRect = vc.getBoundingClientRect();
        positionOverlayFor(video as HTMLElement, box, vcRect);
      },

      removeOverlayFor(video: HTMLVideoElement): void {
        const box = ensureOverlayBox(video.id);
        const info = parseCharSeat(video.id);
        if (info) setEmptySeatImg(box, info.ch, info.seat);
        // leave the box in place (no repositioning) — matches original behavior
      },

      repositionAllOverlays(): void {
        const vcRect = vc.getBoundingClientRect();
        document.querySelectorAll('.overlay-box').forEach((elem) => {
          const box = elem as HTMLElement;
          const vidId = box.id.replace(/^overlay_/, '');
          const v = document.getElementById(vidId) as HTMLElement | null;
          if (v) {
            positionOverlayFor(v, box, vcRect);
          }
        });
      },

      showEmptySeatForId(videoId: string): void {
        const box = ensureOverlayBox(videoId);
        const info = parseCharSeat(videoId);
        if (info) setEmptySeatImg(box, info.ch, info.seat);
      },

      clearEmptySeatForId(videoId: string): void {
        const box = document.getElementById('overlay_' + videoId) as HTMLElement | null;
        if (box) clearEmptySeatImg(box);
      }
    };

    // Attach typed helpers to window
    window.__overlayHelpers__ = helpers;
  })();
  (function () {
      const VC = document.getElementById('videoContainer') as HTMLElement | null;
      const POPUP = document.querySelector('.camera-setting-main.enter-buy-main') as HTMLElement | null;
      if (!VC || !POPUP) return;

      function isVisible(el: HTMLElement): boolean {
        const cs = getComputedStyle(el);
        return (
          cs.display !== 'none' &&
          cs.visibility !== 'hidden' &&
          parseFloat(cs.opacity || '1') > 0
        );
      }

      function canOpenOpenSeat(): boolean {
        // Only when your CSS flag that shows "OPEN" is on
        if (!document.body.classList.contains('show-open-seats')) return false;
        // And when the character layer isn't globally hidden
        if ((VC as HTMLElement).classList.contains('chars-hidden')) return false;
        return true;
      }

      function openPopup(): void {
        document.body.classList.add('show-popup', 'popup-open');
        (POPUP as HTMLElement).style.display = 'block';
        (POPUP as HTMLElement).classList.add('active');
        (POPUP as HTMLElement).setAttribute('aria-hidden', 'false');
      }

      document.addEventListener('click', (e: MouseEvent) => {
        if (!canOpenOpenSeat()) return;

        const x = e.clientX;
        const y = e.clientY;

        // Only trigger when the pointer is inside the VISIBLE OPEN text-box,
        // not just anywhere in the overlay rectangle.
        const overlays = document.querySelectorAll('.overlay-box[data-empty="1"]') as NodeListOf<HTMLElement>;

overlays.forEach((box) => {
  if (!isVisible(box)) return;

  const tb = box.querySelector('.text-box') as HTMLElement | null;
  if (!tb || !isVisible(tb)) return;

  const tr = tb.getBoundingClientRect();
  if (x >= tr.left && x <= tr.right && y >= tr.top && y <= tr.bottom) {
    e.preventDefault();
    e.stopPropagation();
    openPopup();
    return;
  }
});

      });

      // Optional close (uses your existing elements if present)
      document.addEventListener('click', (e: MouseEvent) => {
        const target = e.target as HTMLElement | null;
        const closeEl = target?.closest('.buy-cross, .buy-watch-btn');
        if (!closeEl) return;

        e.preventDefault();
        document.body.classList.remove('show-popup', 'popup-open');
        POPUP.classList.remove('active');
        POPUP.style.display = 'none';
        POPUP.setAttribute('aria-hidden', 'true');
      });
    })();

     (function () {
      const VC = document.getElementById('videoContainer') as HTMLElement | null;
      if (!VC) return;

      function isVisible(el: HTMLElement): boolean {
        const cs = getComputedStyle(el);
        return (
          cs.display !== 'none' &&
          cs.visibility !== 'hidden' &&
          parseFloat(cs.opacity || '1') > 0
        );
      }

      function canShowFinger(): boolean {
        if (!document.body.classList.contains('show-open-seats')) return false;
        if ((VC as HTMLElement).classList.contains('chars-hidden')) return false;
        return true;
      }

      function updateCursor(e: MouseEvent): void {
        if (!canShowFinger()) {
          document.body.style.cursor = '';
          return;
        }

        const x = e.clientX;
        const y = e.clientY;
        let showFinger = false;

        const overlays = document.querySelectorAll('.overlay-box[data-empty="1"]') as NodeListOf<HTMLElement>;

        // ✅ Use forEach for TypeScript compatibility (no NodeList iterator issue)
        overlays.forEach((box) => {
          if (showFinger) return; // already found one
          if (!isVisible(box)) return;

          const tb = box.querySelector('.text-box') as HTMLElement | null;
          if (!tb || !isVisible(tb)) return;

          const tr = tb.getBoundingClientRect();
          if (x >= tr.left && x <= tr.right && y >= tr.top && y <= tr.bottom) {
            showFinger = true;
          }
        });

        document.body.style.cursor = showFinger ? 'pointer' : '';
      }

      document.addEventListener('mousemove', updateCursor, { passive: true });
    })();

     (function () {
      const vcClock = document.getElementById('backgroundVideo') as HTMLVideoElement | null;

      // helper type for window.__overlayHelpers__
      interface OverlayHelpers {
        repositionAllOverlays(): void;
      }

      function tick(): void {
        const helpers = (window as any).__overlayHelpers__ as OverlayHelpers | undefined;
        if (helpers && typeof helpers.repositionAllOverlays === 'function') {
          helpers.repositionAllOverlays();
        }

        // request next frame
        const rVFC = vcClock && (vcClock as any).requestVideoFrameCallback;
        if (rVFC) {
          (rVFC as (cb: FrameRequestCallback) => number).call(vcClock, tick);
        } else {
          requestAnimationFrame(tick);
        }
      }

      if (vcClock && vcClock.readyState >= 1) {
        // metadata already loaded
        tick();
      } else if (vcClock) {
        // wait for video metadata first
        vcClock.addEventListener('loadedmetadata', tick, { once: true });
      } else {
        // fallback if video not found
        requestAnimationFrame(tick);
      }

      // reposition overlays on window resize
      addEventListener(
        'resize',
        () => {
          const helpers = (window as any).__overlayHelpers__ as OverlayHelpers | undefined;
          if (helpers && typeof helpers.repositionAllOverlays === 'function') {
            helpers.repositionAllOverlays();
          }
        },
        { passive: true }
      );
    })();

    
  }
}
