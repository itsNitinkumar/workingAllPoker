/* ===== inline script 1 ===== */
/* === DROP-IN REPLACEMENT: overlay helpers with empty-seat support === */
(function () {
  const vc = document.getElementById('videoContainer');

  function positionOverlayFor(video, box, vcRect) {
    const r = video.getBoundingClientRect();
    box.style.left = (r.left - vcRect.left) + 'px';
    box.style.top = (r.top - vcRect.top) + 'px';
    box.style.width = r.width + 'px';
    box.style.height = r.height + 'px';
  }

  function parseCharSeat(videoId) {
    // video_character{C}_seat{S}
    const m = /video_character(\d+)_seat(\d+)/.exec(videoId);
    return m ? { ch: m[1], seat: m[2] } : null;
  }

  function ensureOverlayBox(videoId) {
    const id = 'overlay_' + videoId;
    let box = document.getElementById(id);
    if (!box) {
      box = document.createElement('div');
      box.id = id;
      box.className = 'overlay-box';
      box.innerHTML = `
      <div class="text-box justify-content-start buy-btn">
        <h5 class="call-main">$10000</h5>
      </div>`;

      vc.appendChild(box);
    }
    return box;
  }

  function setEmptySeatImg(box, ch, seat) {
    // Reuse or create the <img>, style inline so no extra CSS is required.
    let img = box.querySelector('img.empty-seat');
    if (!img) {
      img = document.createElement('img');
      img.className = 'empty-seat';
      img.alt = 'Empty seat';
      Object.assign(img.style, {
        position: 'absolute', inset: '0',
        width: '100%', height: '100%',
        objectFit: 'contain', pointerEvents: 'none',
        filter: 'drop-shadow(0 2px 6px rgba(0,0,0,.35))',
        display: 'block'
      });
      box.appendChild(img);
    }
    img.src = `video/character${ch}-seat${seat}.png`;
    img.style.display = 'block';
    box.dataset.empty = '1';
  }

  function clearEmptySeatImg(box) {
    const img = box.querySelector('img.empty-seat');
    if (img) img.style.display = 'none';
    delete box.dataset.empty;
  }

  window.__overlayHelpers__ = {
    makeOverlay(video) {
      const box = ensureOverlayBox(video.id);
      // If a PNG was showing for this seat, hide it now that the video is back.
      clearEmptySeatImg(box);
      const vcRect = vc.getBoundingClientRect();
      positionOverlayFor(video, box, vcRect);
    },

    // IMPORTANT: when a seat is toggled OFF, we DON'T remove the overlay.
    // We swap it to an "empty chair" PNG and keep it at the last position.
    removeOverlayFor(video) {
      const box = ensureOverlayBox(video.id);
      const info = parseCharSeat(video.id);
      if (info) setEmptySeatImg(box, info.ch, info.seat);
      // Leave the box in place; it will no longer be repositioned once the <video> is removed.
    },

    repositionAllOverlays() {
      const vcRect = vc.getBoundingClientRect();
      document.querySelectorAll('.overlay-box').forEach(box => {
        const vidId = box.id.replace(/^overlay_/, '');
        const v = document.getElementById(vidId);
        // Only actively reposition boxes that still have a live <video>.
        if (v) {
          positionOverlayFor(v, box, vcRect);
        }
      });
    },

    // Optional helpers if you ever want to toggle the PNG manually:
    showEmptySeatForId(videoId) {
      const box = ensureOverlayBox(videoId);
      const info = parseCharSeat(videoId);
      if (info) setEmptySeatImg(box, info.ch, info.seat);
    },
    clearEmptySeatForId(videoId) {
      const box = document.getElementById('overlay_' + videoId);
      if (box) clearEmptySeatImg(box);
    }
  };
})();


/* ===== inline script 2 ===== */
/* OPEN on empty-seat overlay: DISABLED (shows OPEN but does nothing) */
(function () {
  const VC = document.getElementById('videoContainer');
  if (!VC) return;

  // Block clicks ONLY when OPEN is intended to be visible, so normal overlays remain unaffected.
  function openIsEnabled() {
    if (!document.body.classList.contains('show-open-seats')) return false;
    if (VC.classList.contains('chars-hidden')) return false;
    return true;
  }

  document.addEventListener('click', function (e) {
    if (!openIsEnabled()) return;

    // If the click lands on an OPEN label on an empty seat, swallow it.
    const tb = e.target.closest('.overlay-box[data-empty="1"] .text-box');
    if (!tb) return;

    // Only swallow when the label is actually visible.
    const cs = getComputedStyle(tb);
    if (cs.display === 'none' || cs.visibility === 'hidden' || parseFloat(cs.opacity || '1') <= 0) return;

    e.preventDefault();
    e.stopPropagation();
  }, true);

  // Ensure we don't show a finger cursor on OPEN labels anymore.
  document.addEventListener('mousemove', function () {
    if (openIsEnabled()) document.body.style.cursor = '';
  }, { passive: true });
})();


/* ===== inline script 3 ===== */
/* (removed) OPEN cursor helper disabled */

/* ===== inline script 4 ===== */

(function () {
  const vcClock = document.getElementById('backgroundVideo');

  function tick() {
    if (window.__overlayHelpers__) window.__overlayHelpers__.repositionAllOverlays();
    const rVFC = vcClock && vcClock.requestVideoFrameCallback;
    (rVFC ? rVFC.bind(vcClock) : requestAnimationFrame)(tick);
  }

  if (vcClock && vcClock.readyState >= 1) {
    tick();
  } else if (vcClock) {
    vcClock.addEventListener('loadedmetadata', tick, { once: true });
  } else {
    requestAnimationFrame(tick);
  }

  addEventListener('resize', () => {
    if (window.__overlayHelpers__) window.__overlayHelpers__.repositionAllOverlays();
  }, { passive: true });
})();


/* ===== inline script 5 ===== */
document.addEventListener('DOMContentLoaded', function () {
  const videoContainer = document.getElementById('videoContainer');
  const characterSelector = document.getElementById('characterSelector');
  const gestureSelector = document.getElementById('gestureSelector');
  const blindsSelector = document.getElementById('blindsSelector');
  const betAmountInput = document.getElementById('betAmount');
  const resetPotBtn = document.getElementById('resetPotBtn');
  const collectBetsBtn = document.getElementById('collectBetsBtn');
  const potEl = document.getElementById('pot');
  const potImg = document.getElementById('potImg');
  const potBox = document.getElementById('potLayers');

  const defaultSeats = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
  const defaultPlaybackRate = 1.5;

  function makeOverlay(video) { window.__overlayHelpers__.makeOverlay(video); }
  function removeOverlayFor(video) { window.__overlayHelpers__.removeOverlayFor(video); }

  /* ---------- CHIP IMAGE HELPERS ---------- */
  const CHIP_URL = (name) => `img/chips/${name}.png`;

  function betKeyFromAmount(n) {
    if (n <= 200) return 'b200';
    if (n <= 500) return 'b500';
    if (n <= 1000) return 'b1000';
    if (n <= 2000) return 'b2000';
    return 'b5';
  }

  /* ---------- POT STATE ---------- */
  let potTotal = 0;

  function updatePotImage() {
    let key = 'p-blinds';
    if (potTotal > 0) key = 'p-bets';
    if (potTotal <= 500 && potTotal > 0) key = 'p500';
    if (potTotal > 500 && potTotal <= 1000) key = 'p1000';
    if (potTotal > 1000 && potTotal <= 2000) key = 'p2000';
    if (potTotal > 2000) key = 'p-bets';
    potImg.src = CHIP_URL(key);
  }

  // layered pot sprites
  function potLayerKeyFromAmount(n) {
    if (n <= 500) return 'p500';
    if (n <= 1000) return 'p1000';
    if (n <= 2000) return 'p2000';
    return 'p-bets';
  }
  function addPotLayerByKey(key) {
    const img = document.createElement('img');
    img.className = 'pot-layer';
    img.src = CHIP_URL(key);
    const rot = (Math.random() * 6 - 3).toFixed(2) + 'deg';
    img.style.setProperty('--rot', rot);
    potBox.appendChild(img);
    requestAnimationFrame(() => img.classList.add('is-on'));
    rescalePotByLayers();
  }
  function addPotLayerForAmount(amount) {
    addPotLayerByKey(potLayerKeyFromAmount(amount));
  }
  function rescalePotByLayers() {
    const count = potBox.children.length;
    const s = Math.min(1 + count * 0.03, 1.35);
    const hasTranslate = (potEl.style.transform || '').includes('translateY');
    potEl.style.transform = (hasTranslate ? potEl.style.transform.replace(/scale\([^)]+\)/, '') + ' ' : '')
      + `scale(${s})`;
  }

  resetPotBtn.addEventListener('click', () => {
    potTotal = 0;
    while (potBox.firstChild) potBox.removeChild(potBox.firstChild);
    potImg.src = CHIP_URL('p-blinds');
    if (!potEl.style.transform.includes('translateY')) potEl.style.transform = '';
  });

  /* ---------- POT POSITION (manual or auto) ---------- */
  function cssNum(v) {
    v = (v || '').trim();
    if (!v) return 0;
    if (v.endsWith('px')) return parseFloat(v);
    if (v.endsWith('rem')) return parseFloat(v) * 16;
    if (v.endsWith('%')) return window.innerWidth * parseFloat(v) / 100;
    return parseFloat(v) || 0;
  }

  function positionPotAuto() {
    const zr = document.getElementById('zoomArea').getBoundingClientRect();
    const docVars = getComputedStyle(document.documentElement);
    const ox = cssNum(docVars.getPropertyValue('--pot-offset-x'));
    const oy = cssNum(docVars.getPropertyValue('--pot-offset-y'));
    const left = zr.right + ox;
    const top = zr.top + (zr.height / 2) + oy;
    potEl.style.left = left + 'px';
    potEl.style.top = top + 'px';
    potEl.style.width = docVars.getPropertyValue('--pot-width').trim() || '120px';
    const current = potEl.style.transform || '';
    potEl.style.transform = `translateY(-50%)`;
    if (current.includes('scale(')) {
      const m = current.match(/scale\([^)]+\)/);
      if (m) potEl.style.transform += ' ' + m[0];
    }
  }

  function applyPotMode() {
    const mode = getComputedStyle(document.documentElement).getPropertyValue('--pot-mode').trim();
    if (mode === 'auto') {
      positionPotAuto();
      window.addEventListener('resize', positionPotAuto, { passive: true });
    } else {
      window.removeEventListener('resize', positionPotAuto);
      const cs = getComputedStyle(document.documentElement);
      potEl.style.left = cs.getPropertyValue('--pot-left').trim();
      potEl.style.top = cs.getPropertyValue('--pot-top').trim();
      potEl.style.width = cs.getPropertyValue('--pot-width').trim();
      const current = potEl.style.transform || '';
      const m = current.match(/scale\([^)]+\)/);
      potEl.style.transform = m ? m[0] : '';
    }
  }
  applyPotMode();

  /* ======================================================================
 POT WINNER ANIMATION (MULTI-SELECT + DROPDOWN PANEL)
 Show Cards style selector + Award button visible in controller
 ====================================================================== */
  (function () {

    // ===== Animation tuning =====
    const POT_FLY_DURATION_MS = 1200;
    const POT_FADE_OUT_SCALE = 0.7;
    const STACK_PULSE_DURATION = 0;

    const controls = document.getElementById('controls');
    if (!controls) return;

    /* ------------------------------------------------------------------
       UI — Placement (anchor AFTER Show Cards UI)
       ------------------------------------------------------------------ */

    const showCardsPanel = document.getElementById('showCardsSelectPanel');
    const insertAfter = showCardsPanel || controls;

    /* ---------- Toggle Button ---------- */
    let btn = document.getElementById('potWinnersSelectBtn');
    if (!btn) {
      btn = document.createElement('button');
      btn.type = 'button';
      btn.id = 'potWinnersSelectBtn';
      btn.textContent = 'Pot Winners ▾';
      btn.style.cssText =
        'margin-top:0;margin-right:6px;padding:4px 10px;border-radius:8px;' +
        'border:1px solid rgba(255,255,255,.25);' +
        'background:rgba(0,0,0,.25);color:#fff;';
      insertAfter.insertAdjacentElement('afterend', btn);
    }

    /* ---------- Award Button (VISIBLE in controller) ---------- */
    let awardBtn = document.getElementById('awardPotBtn');
    if (!awardBtn) {
      awardBtn = document.createElement('button');
      awardBtn.type = 'button';
      awardBtn.id = 'awardPotBtn';
      awardBtn.textContent = 'Award Pot';
      awardBtn.style.cssText =
        'margin-top:0;padding:4px 12px;border-radius:8px;' +
        'border:1px solid rgba(255,255,255,.35);' +
        'background:#1e1e1e;color:#fff;';
      btn.insertAdjacentElement('afterend', awardBtn);
    }

    /* ---------- Panel ---------- */
    let panel = document.getElementById('potWinnersSelectPanel');
    if (!panel) {
      panel = document.createElement('div');
      panel.id = 'potWinnersSelectPanel';
      panel.style.cssText =
        'display:none;margin-top:8px;padding:10px;border-radius:10px;' +
        'border:1px solid rgba(255,255,255,.18);' +
        'background:rgba(0,0,0,.35);backdrop-filter:blur(10px);max-width:320px;';

      panel.innerHTML = `
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:8px;">
        <button type="button" id="potNoneBtn"
          style="padding:4px 10px;border-radius:8px;border:1px solid rgba(255,255,255,.25);
                 background:rgba(0,0,0,.25);color:#fff;">None</button>
        <button type="button" id="potAllBtn"
          style="padding:4px 10px;border-radius:8px;border:1px solid rgba(255,255,255,.25);
                 background:rgba(0,0,0,.25);color:#fff;">All</button>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;">
        ${Array.from({ length: 9 }, (_, i) => {
        const n = i + 1;
        return `
            <label style="display:flex;gap:6px;align-items:center;margin:0;">
              <input type="checkbox" class="potWinnerChar" value="${n}">
              C${n}
            </label>
          `;
      }).join('')}
      </div>
    `;

      awardBtn.insertAdjacentElement('afterend', panel);
    }

    /* ---------- Toggle behavior ---------- */
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      panel.style.display = (panel.style.display === 'block' ? 'none' : 'block');
    });

    panel.addEventListener('click', (e) => e.stopPropagation());

    document.addEventListener('click', (e) => {
      if (!panel.contains(e.target) && e.target !== btn) {
        panel.style.display = 'none';
      }
    }, true);

    /* ---------- Selector Controls ---------- */
    const noneBtn = panel.querySelector('#potNoneBtn');
    const allBtn = panel.querySelector('#potAllBtn');
    const charCbs = Array.from(panel.querySelectorAll('.potWinnerChar'));

    function setNone() { charCbs.forEach(c => c.checked = false); }
    function setAll() { charCbs.forEach(c => c.checked = true); }

    noneBtn.addEventListener('click', (e) => { e.preventDefault(); setNone(); });
    allBtn.addEventListener('click', (e) => { e.preventDefault(); setAll(); });

    /* ------------------------------------------------------------------
       Animation helpers (unchanged)
       ------------------------------------------------------------------ */

    function seatViewNow() {
      const idx = Number(characterSelector?.value || 1) - 1;
      return (defaultSeats && defaultSeats[idx]) ? defaultSeats[idx] : '1';
    }

    function resetPotNow() {
      potTotal = 0;
      while (potBox.firstChild) potBox.removeChild(potBox.firstChild);
      potImg.src = CHIP_URL('p-blinds');
    }

    function clonePotVisual() {
      const pr = potEl.getBoundingClientRect();

      const fly = document.createElement('div');
      fly.className = 'pot-fly';
      fly.style.cssText = `
      position:fixed;
      left:${pr.left}px;
      top:${pr.top}px;
      width:${pr.width}px;
      height:${pr.height}px;
      pointer-events:none;
      z-index:999999;
      transform: translate3d(0,0,0) scale(1);
      opacity:1;
      will-change: transform, opacity;
    `;

      const img = document.createElement('img');
      img.src = potImg.src;
      img.style.cssText = 'width:100%;height:100%;object-fit:contain;';
      fly.appendChild(img);

      if (potBox && potBox.children.length) {
        const layerWrap = document.createElement('div');
        layerWrap.style.cssText = 'position:absolute;inset:0;';
        Array.from(potBox.children).forEach(child => {
          if (child.tagName === 'IMG') layerWrap.appendChild(child.cloneNode(true));
        });
        fly.appendChild(layerWrap);
      }

      document.body.appendChild(fly);
      return fly;
    }

    function getStackCenterForCharacter(character) {
      const seat = seatViewNow();
      const vidId = `video_character${character}_seat${seat}`;
      const stackId = `stack_${vidId}`;
      const stackEl = document.getElementById(stackId);

      if (stackEl) {
        const r = stackEl.getBoundingClientRect();
        return { el: stackEl, x: r.left + r.width / 2, y: r.top + r.height / 2 };
      }

      const overlay = document.getElementById('overlay_' + vidId);
      if (overlay) {
        const r = overlay.getBoundingClientRect();
        return { el: overlay, x: r.left + r.width / 2, y: r.top + r.height / 2 };
      }

      return null;
    }

    function pulseTarget(el) {
      if (!el) return;
      el.animate(
        [
          { transform: 'scale(1)' },
          { transform: 'scale(1.12)' },
          { transform: 'scale(1)' }
        ],
        { duration: STACK_PULSE_DURATION, easing: 'cubic-bezier(.2,.9,.2,1)' }
      );
    }

    function animatePotToTarget(target, done) {
      const fly = clonePotVisual();
      const fr = fly.getBoundingClientRect();
      const flyCenter = { x: fr.left + fr.width / 2, y: fr.top + fr.height / 2 };

      const dx = target.x - flyCenter.x;
      const dy = target.y - flyCenter.y;

      fly.animate(
        [
          { transform: 'translate3d(0,0,0) scale(1)', opacity: 1 },
          { transform: `translate3d(${dx}px, ${dy}px, 0) scale(${POT_FADE_OUT_SCALE})`, opacity: 0.5 }
        ],
        {
          duration: POT_FLY_DURATION_MS,
          easing: 'cubic-bezier(.15,.9,.2,1)',
          fill: 'forwards'
        }
      ).onfinish = () => {
        fly.remove();
        pulseTarget(target.el);
        done?.();
      };
    }

    /* ------------------------------------------------------------------
       Award logic
       ------------------------------------------------------------------ */

    awardBtn.addEventListener('click', () => {

      if (!potBox || potBox.children.length === 0) return;

      const winners = charCbs
        .filter(cb => cb.checked)
        .map(cb => Number(cb.value))
        .map(getStackCenterForCharacter)
        .filter(Boolean);

      if (!winners.length) return;

      let index = 0;

      function next() {
        if (index >= winners.length) {
          resetPotNow();
          return;
        }

        const target = winners[index++];
        animatePotToTarget(target, () => {
          setTimeout(next, 120);
        });
      }

      next();
    });

  })();



  /* ======================================================================
     AMOUNT-AWARE CHIP STACK DISTRIBUTION 
     ====================================================================== */
  let joinCounter = 0;
  const chipAmounts = new Map();

  function getActiveVideoEls() {
    return Array.from(videoContainer.children).filter(n => n.tagName === 'VIDEO');
  }

  function computeThirdsCounts(n) {
    const base = Math.floor(n / 3);
    let rem = n % 3;
    const A = base + (rem > 0 ? 1 : 0); rem = Math.max(0, rem - 1);
    const B = base + (rem > 0 ? 1 : 0); rem = Math.max(0, rem - 1);
    const C = n - A - B;
    return { A, B, C };
  }

  function rebalanceStacks() {
    const vids = getActiveVideoEls();
    const n = vids.length;
    if (n === 0) return;

    const { A, B, C } = computeThirdsCounts(n);

    const ranked = vids
      .map(v => ({
        v,
        amt: Number(chipAmounts.get(v.id) ?? 0),
        join: Number(v.dataset.joinIndex ?? 1e9)
      }))
      .sort((a, b) => (b.amt - a.amt) || (a.join - b.join));

    ranked.forEach((item, i) => {
      const tier = (i < A) ? 'A' : (i < A + B) ? 'B' : 'C';
      const img = document.getElementById('stack_' + item.v.id);
      const key = (tier === 'A') ? 'cs5-6' : (tier === 'B') ? 'cs3-4' : 'cs1-2';
      if (img) img.src = CHIP_URL(key);
    });
  }

  window.setPlayerChips = function setPlayerChips(character, amount) {
    const seatView = defaultSeats[characterSelector.value - 1];
    const id = `video_character${character}_seat${seatView}`;
    chipAmounts.set(id, Number(amount));
    requestAnimationFrame(rebalanceStacks);
  };

  /* ---------- VIDEOS: create/remove ---------- */
  function updateCharacterView() {
    const selectedCharacter = characterSelector.value;
    const seatView = defaultSeats[selectedCharacter - 1];
    clearVideos();
    generateCheckboxes(selectedCharacter, seatView);
    requestAnimationFrame(rebalanceStacks);
  }

  function generateCheckboxes(selectedCharacter, seatView) {
    const checkboxContainer = document.getElementById('checkboxContainer');
    checkboxContainer.innerHTML = '';
    for (let i = 1; i <= 9; i++) {
      const label = document.createElement('label');
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.className = 'seatCheckbox';
      checkbox.value = i;
      checkbox.checked = (i.toString() === selectedCharacter);
      checkbox.onchange = () => {
        toggleVideo(i, checkbox.checked, seatView);
        requestAnimationFrame(rebalanceStacks);
      };
      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(` C${i} Seat ${seatView}`));
      checkboxContainer.appendChild(label);
      toggleVideo(i, checkbox.checked, seatView);
    }
    requestAnimationFrame(rebalanceStacks);
  }

  function toggleVideo(character, shouldShow, seat) {
    const videoId = `video_character${character}_seat${seat}`;
    let video = document.getElementById(videoId);

    if (shouldShow) {
      if (!video) {
        video = document.createElement('video');
        video.id = videoId;
        video.className = `overlay character${character}-seat${seat}`;
        video.preload = 'auto';
        video.src = `video/character${character}-seat${seat}.webm`;
        video.autoplay = false;
        video.muted = true;
        video.playsInline = true;
        video.loop = false;
        video.playbackRate = defaultPlaybackRate;
        video.setAttribute('decoding', 'async');
        video.setAttribute('disablepictureinpicture', '');
        video.controls = false;

        video.dataset.joinIndex = String(++joinCounter);
        if (!chipAmounts.has(video.id)) chipAmounts.set(video.id, 1000);

        videoContainer.appendChild(video);

        makeOverlay(video);
        attachChipStack(character, seat, video);

      } else {
        makeOverlay(video);
      }
    } else {
      if (video) {
        removeStackAndPlacardFor(video.id);
        removeOverlayFor(video);
        videoContainer.removeChild(video);
      }
      // Ensure overlay exists and shows empty seat PNG
      if (window.__overlayHelpers__ && window.__overlayHelpers__.showEmptySeatForId) {
        window.__overlayHelpers__.showEmptySeatForId(videoId);
      }
    }
  }

  function clearVideos() {
    while (videoContainer.firstChild) {
      const node = videoContainer.firstChild;
      if (node.tagName === 'VIDEO') {
        removeStackAndPlacardFor(node.id);
        removeOverlayFor(node);
      }
      videoContainer.removeChild(node);
    }
  }

  /* ---------- CHIP STACKS ---------- */
  function attachChipStack(character, seat, videoEl) {
    const stackId = `stack_${videoEl.id}`;
    if (document.getElementById(stackId)) return;

    const img = document.createElement('img');
    img.id = stackId;
    img.className = `chip-stack stack-character${character}-seat${seat}`;
    img.alt = 'chip stack';
    img.src = CHIP_URL('cs3-4');

    const overlayId = 'overlay_' + videoEl.id;
    if (!document.getElementById(overlayId)) makeOverlay(videoEl);
    const overlay = document.getElementById(overlayId);
    overlay.appendChild(img);
  }

  function removeStackAndPlacardFor(videoId) {
    const s = document.getElementById('stack_' + videoId);
    if (s) s.remove();
    const p = document.getElementById('placard_' + videoId);
    if (p) p.remove();
  }

  /* ---------- BET PLACARD (front of video) ---------- */
  function ensureBetPlacard(character, seat, videoEl, betKey) {
    const placardId = `placard_${videoEl.id}`;
    let el = document.getElementById(placardId);
    if (!el) {
      el = document.createElement('img');
      el.id = placardId;
      el.className = `bet-placard bet-character${character}-seat${seat}`;
      const overlayId = 'overlay_' + videoEl.id;
      if (!document.getElementById(overlayId)) makeOverlay(videoEl);
      document.getElementById(overlayId).appendChild(el);
    }
    el.src = CHIP_URL(betKey);
    requestAnimationFrame(() => el.classList.add('is-on'));
    return el;
  }

  /* ---------- POSITION HELPERS ---------- */
  function positionElementAtVideo(el, videoEl, anchor) {
    const vc = videoContainer.getBoundingClientRect();
    const r = videoEl.getBoundingClientRect();
    const x = r.left - vc.left + r.width * (anchor?.x ?? .5) - (el.width / 2 || 50);
    const y = r.top - vc.top + r.height * (anchor?.y ?? 1.0) - (el.height || 60);
    el.style.left = `${x}px`;
    el.style.top = `${y}px`;
  }

  function positionElementAtPlacard(el, placardEl) {
    const vc = videoContainer.getBoundingClientRect();
    const r = placardEl.getBoundingClientRect();
    const x = r.left - vc.left + (r.width / 2) - (el.width / 2 || 50);
    const y = r.top - vc.top + (r.height / 2) - (el.height / 2 || 60);
    el.style.left = `${x}px`;
    el.style.top = `${y}px`;
  }

  /* ---------- pot landing anchor helper ---------- */
  function getPotHitPoint(videoContainer, potEl) {
    const vc = videoContainer.getBoundingClientRect();
    const target = document.getElementById('potTarget');
    const pr = (target ? target.getBoundingClientRect() : potEl.getBoundingClientRect());
    const cx = pr.left + pr.width / 2 - vc.left;
    const cy = pr.top + pr.height / 2 - vc.top;
    return { x: cx, y: cy };
  }

  window.addEventListener('resize', () => {
    const mode = getComputedStyle(document.documentElement).getPropertyValue('--pot-mode').trim();
    if (mode === 'auto') positionPotAuto();
  }, { passive: true });

  /* ---------- CHIP FLIGHT (placard → pot) ---------- */
  function flyChipFromVideoToPot(videoEl, chipKey) {
    const img = document.createElement('img');
    img.className = 'chip-fly';
    img.src = CHIP_URL(chipKey);
    videoContainer.appendChild(img);

    // start from the video (slightly above its center)
    positionElementAtVideo(img, videoEl, { x: .5, y: .35 });

    const vc = videoContainer.getBoundingClientRect();
    const ir = img.getBoundingClientRect();
    const start = { x: ir.left - vc.left, y: ir.top - vc.top };

    const end = getPotHitPoint(videoContainer, potEl);
    const tx = end.x - start.x;
    const ty = end.y - start.y;

    img.style.transition = `
        opacity var(--chip-fade-duration, .25s) ease,
        transform var(--chip-flight-duration, .8s) cubic-bezier(.25,.8,.25,1)
      `;
    img.style.willChange = 'transform, opacity';
    img.style.transform = 'translate3d(0,0,0) scale(.9)';
    img.style.opacity = '1';

    void img.offsetWidth; // force layout

    requestAnimationFrame(() => {
      img.style.transform = `translate3d(${tx}px, ${ty}px, 0) scale(1)`;
    });

    const flightSec = parseFloat(getComputedStyle(img).getPropertyValue('--chip-flight-duration')) || 0.8;
    setTimeout(() => { img.style.opacity = '0'; setTimeout(() => img.remove(), 280); }, Math.round(flightSec * 1000));
  }

  function flyChipFromPlacardToPot(placardEl, chipKey) {
    const img = document.createElement('img');
    img.className = 'chip-fly';
    img.src = CHIP_URL(chipKey);
    videoContainer.appendChild(img);

    positionElementAtPlacard(img, placardEl);

    const vc = videoContainer.getBoundingClientRect();
    const ir = img.getBoundingClientRect();
    const start = { x: ir.left - vc.left, y: ir.top - vc.top };
    const end = getPotHitPoint(videoContainer, potEl);
    const tx = end.x - start.x;
    const ty = end.y - start.y;

    img.style.transition = `
        opacity var(--chip-fade-duration, .25s) ease,
        transform var(--chip-flight-duration, .8s) cubic-bezier(.25,.8,.25,1)
      `;
    img.style.willChange = 'transform, opacity';
    img.style.transform = 'translate3d(0,0,0) scale(.9)';
    img.style.opacity = '1';

    void img.offsetWidth;
    requestAnimationFrame(() => {
      img.style.transform = `translate3d(${tx}px, ${ty}px, 0) scale(1)`;
    });

    const flightSec = parseFloat(getComputedStyle(img).getPropertyValue('--chip-flight-duration')) || 0.8;
    setTimeout(() => { img.style.opacity = '0'; setTimeout(() => img.remove(), 280); }, Math.round(flightSec * 1000));
  }

  function cssTime(val) {
    const v = (val || '').trim();
    if (v.endsWith('ms')) return parseFloat(v);
    if (v.endsWith('s')) return parseFloat(v) * 1000;
    const n = parseFloat(v);
    return isNaN(n) ? 650 : n;
  }

  /* ================================================================
     HELPER: GET CHECKED VIDEOS + STACK ELEMENTS
     ================================================================ */
  function getCheckedVideos() {
    const seatView = defaultSeats[characterSelector.value - 1];
    const checked = Array.from(document.querySelectorAll('#checkboxContainer .seatCheckbox'))
      .filter(c => c.checked)
      .map(c => Number(c.value));
    return checked
      .map(ch => document.getElementById(`video_character${ch}_seat${seatView}`))
      .filter(Boolean);
  }
  function getStackElByVideoId(videoId) {
    return document.getElementById('stack_' + videoId);
  }

  function msFromCss(el, varName, fallbackMs) {
    const raw = getComputedStyle(el).getPropertyValue(varName).trim();
    if (!raw) return fallbackMs;
    if (raw.endsWith('ms')) return parseFloat(raw);
    if (raw.endsWith('s')) return parseFloat(raw) * 1000;
    const n = parseFloat(raw);
    return isNaN(n) ? fallbackMs : n;
  }

  /* ================================================================
     ALL-IN: BUMP → PUSH (NO POT MOVEMENT YET)
     ================================================================ */
  function bumpThenPushStack(stackEl) {
    if (!stackEl) return;

    // retrigger bump cleanly
    stackEl.classList.remove('allin-bump', 'allin-push');
    void stackEl.offsetWidth;
    stackEl.classList.add('is-allin-armed');
    stackEl.classList.add('allin-bump');

    // IMPORTANT: read times from the stack itself (per-stack overrides work)
    const bumpDurMs = msFromCss(stackEl, '--allin-bump-duration', 120);
    const bumpDelayMs = msFromCss(stackEl, '--allin-bump-delay', 0);

    // Only start the push after bump delay + bump duration
    setTimeout(() => {
      stackEl.classList.add('allin-push');
    }, bumpDelayMs + bumpDurMs + 16);
  }




  /* ================================================================
     ALL-IN: STACK → POT ON "BETTING"
     - Slides the entire stack to the pot hit-point, fades it out,
       updates pot, zeroes player's chips, and rebalances.
     ================================================================ */
  function shoveStackIntoPot(videoEl) {
    const stackEl = getStackElByVideoId(videoEl.id);
    if (!stackEl) return;

    // Start from the BOTTOM-CENTER of the stack (matches how the pot "feels")
    const vcRect = videoContainer.getBoundingClientRect();
    const sr = stackEl.getBoundingClientRect();
    const start = {
      x: sr.left + sr.width / 2 - vcRect.left,
      y: sr.bottom - vcRect.top
    };

    // Same pot landing point used by the bet chip flights
    const end = getPotHitPoint(videoContainer, potEl);

    // Optional small nudge via CSS vars (so you can tweak without code)
    const cs = getComputedStyle(document.documentElement);
    const offX = parseFloat(cs.getPropertyValue('--stack-hit-dx')) || 0;
    const offY = parseFloat(cs.getPropertyValue('--stack-hit-dy')) || 0;

    // Delta to travel (set via 'translate' so we don't clobber anchor transforms)
    const dx = Math.round((end.x + offX) - start.x);
    const dy = Math.round((end.y + offY) - start.y);

    // Animate the shove
    stackEl.classList.add('moving-to-pot');
    stackEl.style.translate = `${dx}px ${dy}px`;

    // Arrival → fade, update pot total, zero player's chips, reset element
    const durS = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--stack-to-pot-duration')) || 0.65;
    setTimeout(() => {
      // indicate chips are gone
      stackEl.style.opacity = '0';

      // move the player's full stack into the pot
      const playerAmt = Number(chipAmounts.get(videoEl.id) || 0);
      const addAmt = Math.max(0, playerAmt);
      if (addAmt > 0) {
        potTotal += addAmt;
        addPotLayerForAmount(addAmt);
        updatePotImage();
        chipAmounts.set(videoEl.id, 0);
      }

      // reset for reuse next hand
      setTimeout(() => {
        stackEl.classList.remove('moving-to-pot', 'allin-bump', 'allin-push', 'is-allin-armed');
        stackEl.style.translate = '';
        stackEl.style.opacity = '';
        rebalanceStacks();
      }, 240);

    }, Math.round(durS * 1000));
  }

  /* ================================================================
     GESTURES (Note: All-In path uses stack-only animation)
     ================================================================ */
  // --- Idle-only playbackRate override ---
  const IDLE_VALUE = '30.50';   // matches <option value="30.50">Idle...</option>
  const IDLE_RATE = 0.75;      // tweak to taste

  function applyGesturePlaybackRate(targets) {
    const rate = (gestureSelector.value === IDLE_VALUE) ? IDLE_RATE : defaultPlaybackRate;
    // apply ONLY to the gesture targets (prevents "all characters animate")
    (targets || []).forEach(el => {
      if (el && el.tagName === 'VIDEO') el.playbackRate = rate;
    });
  }

  /* ---------- GESTURE / EMOJI CONTROL (single target) ---------- */
  // Mode is either: 'single' (one character) or 'checked' (use seat checkboxes)
  window.__gestureControl__ = window.__gestureControl__ || { mode: 'single', ch: String(characterSelector?.value || 1) };

  function getGestureTargets() {
    const seatView = defaultSeats[Number(characterSelector?.value || 1) - 1] || '1';
    const ctl = window.__gestureControl__ || { mode: 'single', ch: '1' };

    if (ctl.mode === 'checked') {
      const vids = (typeof getCheckedVideos === 'function') ? getCheckedVideos() : [];
      return vids;
    }

    const ch = Number(ctl.ch || characterSelector?.value || 1);
    const v = document.getElementById(`video_character${ch}_seat${seatView}`);
    return v ? [v] : [];
  }

  // Expose so the emoji script can target the same character
  window.__getGestureTargets = getGestureTargets;

  function mountGestureControlUI() {
    const controls = document.getElementById('controls');
    const gestureSel = document.getElementById('gestureSelector');
    if (!controls || !gestureSel) return;

    // Place right after the Gesture select label (so it's always near gestures)
    const gestureLabel = gestureSel.closest('label') || gestureSel.parentElement || controls;

    let btn = document.getElementById('gestureControlBtn');
    if (!btn) {
      btn = document.createElement('button');
      btn.type = 'button';
      btn.id = 'gestureControlBtn';
      btn.textContent = 'Control ▾';
      btn.style.cssText =
        'margin-left:10px;padding:4px 10px;border-radius:8px;' +
        'border:1px solid rgba(255,255,255,.25);background:rgba(0,0,0,.25);color:#fff;';
      gestureLabel.insertAdjacentElement('afterend', btn);
    }

    let panel = document.getElementById('gestureControlPanel');
    if (!panel) {
      panel = document.createElement('div');
      panel.id = 'gestureControlPanel';
      panel.style.cssText =
        'display:none;margin-top:8px;padding:10px;border-radius:10px;' +
        'border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.35);' +
        'backdrop-filter:blur(10px);max-width:320px;';

      panel.innerHTML = `
              <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:8px;">
                <label style="margin:0;display:flex;gap:6px;align-items:center;">
                  <input type="radio" name="gestureCtrlMode" value="single" checked> One
                </label>
                <label style="margin:0;display:flex;gap:6px;align-items:center;">
                  <input type="radio" name="gestureCtrlMode" value="checked"> Checked
                </label>
              </div>
              <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;">
                ${Array.from({ length: 9 }, (_, i) => {
        const n = i + 1;
        return `
                    <label style="display:flex;gap:6px;align-items:center;margin:0;">
                      <input type="radio" name="gestureCtrlChar" value="${n}">
                      C${n}
                    </label>
                  `;
      }).join('')}
              </div>
            `;
      btn.insertAdjacentElement('afterend', panel);
    }

    // Defaults: control the current view/selected character
    const initialCh = String(characterSelector?.value || 1);
    panel.querySelectorAll('input[name="gestureCtrlChar"]').forEach(r => {
      r.checked = (r.value === initialCh);
    });
    window.__gestureControl__.mode = 'single';
    window.__gestureControl__.ch = initialCh;

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      panel.style.display = (panel.style.display === 'block' ? 'none' : 'block');
    });
    panel.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', (e) => {
      if (!panel.contains(e.target) && e.target !== btn) panel.style.display = 'none';
    }, true);

    // Update mode
    panel.querySelectorAll('input[name="gestureCtrlMode"]').forEach(r => {
      r.addEventListener('change', () => {
        window.__gestureControl__.mode = r.value;
      });
    });

    // Update character
    panel.querySelectorAll('input[name="gestureCtrlChar"]').forEach(r => {
      r.addEventListener('change', () => {
        window.__gestureControl__.ch = r.value;
        // Force mode to single when a character is explicitly selected
        window.__gestureControl__.mode = 'single';
        const one = panel.querySelector('input[name="gestureCtrlMode"][value="single"]');
        if (one) one.checked = true;
      });
    });

    // When view changes, snap control target to that view (keeps UX predictable)
    characterSelector?.addEventListener('change', () => {
      const ch = String(characterSelector.value || 1);
      window.__gestureControl__.ch = ch;
      window.__gestureControl__.mode = 'single';
      const one = panel.querySelector('input[name="gestureCtrlMode"][value="single"]');
      if (one) one.checked = true;
      panel.querySelectorAll('input[name="gestureCtrlChar"]').forEach(r => {
        r.checked = (r.value === ch);
      });
    });
  }

  mountGestureControlUI();

  /* ================================================================
     GESTURES (Note: All-In path uses stack-only animation)
     ================================================================ */
  function triggerGesture() {
    triggerGesture._selected = gestureSelector.value;
    if (triggerGesture._raf) cancelAnimationFrame(triggerGesture._raf);

    triggerGesture._raf = requestAnimationFrame(() => {
      triggerGesture._raf = 0;

      const gestureTime = parseFloat(triggerGesture._selected);

      const __targets = (typeof getGestureTargets === 'function') ? getGestureTargets() : [];

      applyGesturePlaybackRate(__targets);

      (__targets || []).forEach(video => {
        if (video.tagName !== 'VIDEO') return;

        if (video.__pauseTimer) {
          clearTimeout(video.__pauseTimer);
          video.__pauseTimer = null;
        }

        video.loop = false;
        video.currentTime = gestureTime;
        video.play();

        const gestureEndTimes = {
          0.00: 3.00,
          3.00: 7.00,
          7.00: 9.50,
          9.50: 14.50,
          14.50: 18.00,
          18.00: 21.50,
          21.50: 26.00,
          26.00: 30.50,
          30.50: 40.00
        };

        const rate = video.playbackRate || 1;
        const endAt = gestureEndTimes[gestureTime];
        const ms = Math.max(0, (endAt - gestureTime) / rate * 1000);

        video.__pauseTimer = setTimeout(() => {
          video.pause();
          video.currentTime = endAt;
          video.__pauseTimer = null;
        }, ms);
      });

      /* ---------------------------------------------
         BET: stage placards (unchanged, no collection)
         --------------------------------------------- */
      if (triggerGesture._selected === '3.00' || triggerGesture._selected === '3.00') {
        stageBetsAndBlinds(__targets);
        return;
      }

      /* ---------------------------------------------
         ALL-IN: animate stacks only (no placards, no chips flight)
         --------------------------------------------- */
      if (triggerGesture._selected === '30.0' || triggerGesture._selected === '26.00') {
        const vids = (__targets && __targets.length) ? __targets : getGestureTargets();
        vids.forEach(v => {
          const stack = getStackElByVideoId(v.id);
          if (stack) bumpThenPushStack(stack);
        });
        // Enable Betting so user can confirm shove to pot
        collectBetsBtn.disabled = vids.length === 0;
      }
    });
  }

  /* ====== Staged betting workflow (UNCHANGED for Bet) ====== */
  const stagedBets = []; // { ch, seatView, videoEl, placardEl, betVal, betKey, isBlind, blindKey }

  function stageBetsAndBlinds(targetVideos) {
    stagedBets.length = 0;

    const betVal = Math.max(1, parseInt(betAmountInput.value || '1', 10));
    const betKey = betKeyFromAmount(betVal);
    const blindKey = blindsSelector.value; // "none", "sb", "bb"

    // IMPORTANT: bets should follow the same "Control" target logic as gestures/emojis
    const vids = (targetVideos && targetVideos.length)
      ? targetVideos
      : ((typeof getGestureTargets === 'function') ? getGestureTargets() : []);

    if (!vids.length) return;

    vids.forEach((videoEl, idx) => {
      if (!videoEl || videoEl.tagName !== 'VIDEO') return;

      const m = /video_character(\d+)_seat(\d+)/.exec(videoEl.id);
      if (!m) return;

      const ch = Number(m[1]);
      const seatView = m[2];

      const placardEl = ensureBetPlacard(ch, seatView, videoEl, betKey);

      stagedBets.push({
        ch,
        seatView,
        videoEl,
        placardEl,
        betVal,
        betKey,
        isBlind: (blindKey !== 'none' && idx === 0),
        blindKey
      });
    });

    collectBetsBtn.disabled = stagedBets.length === 0;
  }

  /* ================================================================
     COLLECT ON "BETTING":
     - Sends staged Bet placards to pot (as before)
     - ALSO shoves any All-In stacks that are armed
     ================================================================ */
  function collectStagedBets() {
    const hadStaged = stagedBets.length > 0;

    if (stagedBets.length > 0) {
      collectBetsBtn.disabled = true;

      const rootCS = getComputedStyle(document.documentElement);

      const perStaggerMs = (function () {
        const v = rootCS.getPropertyValue('--collect-stagger').trim();
        if (!v) return 0;
        if (v.endsWith('ms')) return parseFloat(v);
        if (v.endsWith('s')) return parseFloat(v) * 1000;
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
      })();

      // NEW: read the preflight hold + extra hold
      const preHoldMs = (function () {
        const v = rootCS.getPropertyValue('--bet-preflight-hold').trim();
        if (!v) return 0;
        if (v.endsWith('ms')) return parseFloat(v);
        if (v.endsWith('s')) return parseFloat(v) * 1000;
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
      })();

      const extraHoldMs = (function () {
        const v = rootCS.getPropertyValue('--placard-extra-hold').trim();
        if (!v) return 0;
        if (v.endsWith('ms')) return parseFloat(v);
        if (v.endsWith('s')) return parseFloat(v) * 1000;
        const n = parseFloat(v);
        return isNaN(n) ? 0 : n;
      })();

      const blindDelayMs = (function () {
        const v = rootCS.getPropertyValue('--blind-delay').trim();
        if (!v) return 60;
        if (v.endsWith('ms')) return parseFloat(v);
        if (v.endsWith('s')) return parseFloat(v) * 1000;
        const n = parseFloat(v);
        return isNaN(n) ? 60 : n;
      })();

      const flightMs = (function () {
        const v = rootCS.getPropertyValue('--chip-flight-duration').trim();
        if (!v) return 800 + 140;
        const base = v.endsWith('ms') ? parseFloat(v)
          : v.endsWith('s') ? parseFloat(v) * 1000
            : (parseFloat(v) * 1000 || 800);
        return Math.round(base) + 140; // settle/fade padding
      })();

      let totalAdded = 0;

      stagedBets.forEach((entry, idx) => {
        // OLD: const delayMs = idx * perStaggerMs;
        // NEW: include a preflight hold before each player's launch
        const delayMs = idx * perStaggerMs + preHoldMs;

        setTimeout(() => {
          // launch main bet
          flyChipFromPlacardToPot(entry.placardEl, entry.betKey);
          totalAdded += entry.betVal;

          // optional blind chip
          if (entry.isBlind) {
            setTimeout(() => {
              flyChipFromPlacardToPot(entry.placardEl, entry.blindKey);
            }, blindDelayMs);
          }

          // cleanup & pot visual layers AFTER the chip reaches pot
          setTimeout(() => {
            addPotLayerForAmount(entry.betVal);
            if (entry.isBlind) addPotLayerByKey('p-blinds');

            // keep placard a touch longer if desired
            setTimeout(() => {
              if (entry.placardEl) {
                entry.placardEl.classList.remove('is-on');
                setTimeout(() => entry.placardEl.remove(), 220);
              }
            }, extraHoldMs);

          }, flightMs);

        }, delayMs);
      });

      // finalize pot after last player's timeline completes
      const lastDelay = (stagedBets.length - 1) * perStaggerMs + preHoldMs;
      const totalDuration = lastDelay + flightMs + extraHoldMs + 60;

      setTimeout(() => {
        potTotal += totalAdded;
        updatePotImage();
        stagedBets.length = 0;

        // Then also shove any armed All-In stacks
        collectAllInStacks();
      }, totalDuration);

    } else {
      // No normal bets — maybe All-In stacks are armed:
      collectAllInStacks();
    }
  }


  /* ================================================================
     COLLECT ALL-IN STACKS (if any are armed)
     ================================================================ */
  function collectAllInStacks() {
    const vids = (typeof getGestureTargets === 'function') ? getGestureTargets() : getCheckedVideos();
    const armed = vids
      .map(v => ({ v, stack: getStackElByVideoId(v.id) }))
      .filter(x => x.stack && x.stack.classList.contains('is-allin-armed'));

    if (armed.length === 0) {
      collectBetsBtn.disabled = true;
      return;
    }

    // Shove all in simultaneously
    armed.forEach(({ v, stack }) => shoveStackIntoPot(v));

    // Disable Betting until next staging/gesture
    const durS = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--stack-to-pot-duration')) || 0.65;
    setTimeout(() => { collectBetsBtn.disabled = true; }, Math.round(durS * 1000) + 120);
  }

  // expose
  window.triggerGesture = triggerGesture;
  window.updateCharacterView = updateCharacterView;

  // events
  gestureSelector.addEventListener('change', triggerGesture);
  collectBetsBtn.addEventListener('click', collectStagedBets);
  characterSelector.addEventListener('change', updateCharacterView);

  updateCharacterView(); // Initialize default view
});

/* ================================================================
 POT TEXT ANCHOR
 ================================================================ */
(function () {

  let potLabelEl = null;

  function ensurePotLabel() {
    if (potLabelEl) return potLabelEl;

    potLabelEl = document.createElement('div');
    potLabelEl.className = 'pot-text-anchor';
    potLabelEl.innerHTML = `
      <div class="text-box justify-content-start buy-btn">
        <h5 class="call-main  pot-call-main" id="potTextValue">$10000</h5>
      </div>
    `;

    document.body.appendChild(potLabelEl);
    return potLabelEl;
  }

  function positionPotLabel() {
    const pot = document.getElementById('pot');
    if (!pot) return;

    const label = ensurePotLabel();
    const r = pot.getBoundingClientRect();

    const centerX = r.left + (r.width / 2);
    const topY = r.top;

    label.style.left = `${centerX}px`;
    label.style.top = `${topY - 5}px`;   // small gap above pot
  }

  // Keep it synced continuously (pot moves during zoom / resize / animation)
  function tick() {
    positionPotLabel();
    requestAnimationFrame(tick);
  }

  tick();

  // Public helper so you can change text dynamically later
  window.setPotLabelText = function (text) {
    const el = document.getElementById('potTextValue');
    if (el) el.textContent = text;
  };

})();


/* ===== inline script 6 ===== */
(() => {
  const MAX_RADIUS = 10; // px, circular clamp
  const videoContainer = document.getElementById('videoContainer');

  let isDragging = false;
  let anchor = { x: 0, y: 0 };
  let delta = { x: 0, y: 0 };

  function applyOffsetToAll(dx, dy) {
    // 1) Move all live videos 
    const vids = videoContainer.querySelectorAll('video');
    vids.forEach(v => {
      v.style.transform = `translate(${dx}px, ${dy}px)`;
    });

    // 2) Keep overlay boxes for LIVE seats in sync via your existing helper
    if (window.__overlayHelpers__) {
      window.__overlayHelpers__.repositionAllOverlays();
    }

    // 3) NEW: Move EMPTY seats directly (no live <video> to read a rect from)
    //    Only touch boxes marked as empty so checked seats remain rect-driven.
    const emptyBoxes = videoContainer.querySelectorAll('.overlay-box[data-empty="1"]');
    emptyBoxes.forEach(box => {
      box.style.transform = `translate(${dx}px, ${dy}px)`;
    });

    // 4) (Optional hygiene) Ensure non-empty overlay boxes are NOT double-moved
    //    since their position already follows videos via repositionAllOverlays().
    const nonEmptyBoxes = videoContainer.querySelectorAll('.overlay-box:not([data-empty="1"])');
    nonEmptyBoxes.forEach(box => {
      // avoid accumulating transforms on active seats
      box.style.transform = 'none';
    });
  }

  function clamp(dx, dy) {
    const len = Math.hypot(dx, dy) || 0;
    if (len <= MAX_RADIUS) return { x: dx, y: dy };
    const s = MAX_RADIUS / len;
    return { x: dx * s, y: dy * s };
  }

  function getVideoUnderPointer(evt) {
    if (evt.target && evt.target.tagName === 'VIDEO') return evt.target;
    const el = document.elementFromPoint(evt.clientX, evt.clientY);
    if (el && el.tagName === 'VIDEO') return el;
    return null;
  }

  function onPointerDown(evt) {
    if (evt.button !== 0) return; // left button only
    const vid = getVideoUnderPointer(evt);
    if (!vid) return;

    isDragging = true;
    anchor.x = evt.clientX;
    anchor.y = evt.clientY;
    evt.preventDefault();
  }

  function onPointerMove(evt) {
    if (!isDragging) return;
    const rawDx = evt.clientX - anchor.x;
    const rawDy = evt.clientY - anchor.y;
    delta = clamp(rawDx, rawDy);
    applyOffsetToAll(delta.x, delta.y);
  }

  function onPointerUp() { isDragging = false; }

  videoContainer.addEventListener('mousedown', onPointerDown);
  window.addEventListener('mousemove', onPointerMove);
  window.addEventListener('mouseup', onPointerUp);

  // Touch
  videoContainer.addEventListener('touchstart', (e) => {
    if (!e.touches[0]) return;
    const fake = { button: 0, clientX: e.touches[0].clientX, clientY: e.touches[0].clientY, target: e.target };
    onPointerDown(fake);
  }, { passive: false });

  window.addEventListener('touchmove', (e) => {
    if (!isDragging || !e.touches[0]) return;
    const fake = { clientX: e.touches[0].clientX, clientY: e.touches[0].clientY };
    onPointerMove(fake);
  }, { passive: false });

  window.addEventListener('touchend', onPointerUp);
})();


/* ===== inline script 7 ===== */
(() => {
  const area = document.getElementById('zoomArea');

  // prefer .table (video or img); fallback to existing .tabletop img
  function getZoomEl() {
    return document.querySelector('.table') || document.querySelector('.tabletop');
  }

  const css = getComputedStyle(document.documentElement);
  const Z_MAX = parseFloat(css.getPropertyValue('--zoom-max')) || 3.0;
  const SPEED_IN = parseFloat(css.getPropertyValue('--zoom-in-speed')) || 1.4;   // scale/sec
  const SPEED_OUT = parseFloat(css.getPropertyValue('--zoom-out-speed')) || 2.0; // scale/sec

  let target = getZoomEl();
  let scale = 1;
  let holding = false;
  let holdY = 0;
  let rafId = 0;
  let lastTs = 0;

  // if table element appears later (e.g., swapped to a <video class="table">)
  const observer = new MutationObserver(() => { target = getZoomEl(); });
  observer.observe(document.body, { childList: true, subtree: true });

  function computeTranslateY(viewportY, currentScale, el) {
    if (!el) return 0;
    const r = el.getBoundingClientRect();
    const centerY = r.top + r.height / 2; // pan relative to element’s visual center
    const dy = viewportY - centerY;
    return -(currentScale - 1) * dy;
  }

  function applyTransform() {
    if (!target) return;
    const ty = computeTranslateY(holdY, scale, target);
    target.style.transform = `translate3d(0, ${ty}px, 0) scale(${scale})`;
  }

  function tick(ts) {
    if (!lastTs) lastTs = ts;
    const dt = Math.min(0.05, (ts - lastTs) / 1000);
    lastTs = ts;

    const speed = holding ? SPEED_IN : SPEED_OUT;
    const dir = holding ? 1 : -1;

    if (holding && scale < Z_MAX) {
      scale = Math.min(Z_MAX, scale + speed * dt);
    } else if (!holding && scale > 1) {
      scale = Math.max(1, scale + dir * speed * dt);
    }

    applyTransform();

    if ((holding && scale < Z_MAX) || (!holding && scale > 1)) {
      rafId = requestAnimationFrame(tick);
    } else {
      cancelAnimationFrame(rafId);
      rafId = 0;
      if (!holding) area.style.cursor = 'zoom-in';
    }
  }

  function startHold(y) {
    if (!target) target = getZoomEl();
    if (!target) return;
    holdY = y;
    if (scale <= 1) area.style.cursor = 'zoom-out';
    holding = true;
    if (!rafId) { lastTs = 0; rafId = requestAnimationFrame(tick); }
  }

  function updateHold(y) {
    holdY = y;
    if (!rafId) { lastTs = 0; rafId = requestAnimationFrame(tick); }
  }

  function endHold() {
    holding = false;
    if (!rafId && scale > 1) { lastTs = 0; rafId = requestAnimationFrame(tick); }
  }

  // Mouse
  area.addEventListener('mousedown', (e) => {
    const r = area.getBoundingClientRect();
    if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) return;
    startHold(e.clientY);
  });
  window.addEventListener('mousemove', (e) => { if (holding) updateHold(e.clientY); }, { passive: true });
  window.addEventListener('mouseup', endHold);

  // Touch
  area.addEventListener('touchstart', (e) => {
    if (!e.touches[0]) return;
    const t = e.touches[0];
    startHold(t.clientY);
    e.preventDefault();
  }, { passive: false });
  window.addEventListener('touchmove', (e) => { if (holding && e.touches[0]) updateHold(e.touches[0].clientY); }, { passive: true });
  window.addEventListener('touchend', endHold);
  window.addEventListener('touchcancel', endHold);

  // Reset on Esc
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      scale = 1; applyTransform(); endHold();
    }
  });

  // Re-apply on resize
  window.addEventListener('resize', applyTransform, { passive: true });
})();


/* ===== inline script 8 ===== */
(() => {
  const FADE_DELAY = 0.50;
  const FADE_DURATION = 0.50;

  const container = document.getElementById('videoContainer');
  const area = document.getElementById('zoomArea');

  function setFadeVars(delaySec, durSec) {
    container.style.setProperty('--chars-fade-delay', `${Math.max(0, delaySec)}s`);
    container.style.setProperty('--chars-fade-duration', `${Math.max(0.01, durSec)}s`);
  }

  function hideInstant() {
    container.classList.add('no-fade');
    container.classList.add('chars-hidden');
    requestAnimationFrame(() => container.classList.remove('no-fade'));
  }

  function fadeInWithSettings() {
    setFadeVars(FADE_DELAY, FADE_DURATION);
    void container.offsetWidth; /* reflow */
    container.classList.remove('chars-hidden');
  }

  area.addEventListener('mousedown', hideInstant);
  window.addEventListener('mouseup', fadeInWithSettings);
  area.addEventListener('touchstart', hideInstant, { passive: true });
  window.addEventListener('touchend', fadeInWithSettings, { passive: true });
  window.addEventListener('touchcancel', fadeInWithSettings, { passive: true });

  document.addEventListener('dblclick', () => {
    container.classList.add('no-fade');
    container.classList.remove('chars-hidden');
    requestAnimationFrame(() => container.classList.remove('no-fade'));
  }, { passive: true });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      container.classList.add('no-fade');
      container.classList.remove('chars-hidden');
      requestAnimationFrame(() => container.classList.remove('no-fade'));
    }
  });
})();


/* ===== inline script 9 ===== */
(() => {
  const VC = document.getElementById('videoContainer');
  if (!VC) return;

  /* ===== SHOW TIMING (only when chars-hidden flips) ===== */
  const SHOW_DELAY_S = 0.60;
  const SHOW_DURATION_S = 0.60;

  // Optional per-element overrides:
  //   data-fade-delay="0.8"  data-fade-duration="1.2"

  /* ===== Targets ===== */
  const SELECTORS = [
    '.chip-stack.stack-character3-seat1:not(.chip-stack-ghost)',
    '.chip-stack.stack-character7-seat1:not(.chip-stack-ghost)',
    '.chip-stack.stack-character8-seat1:not(.chip-stack-ghost)',
    '.chip-stack.demoted.stack-character3-seat1',
    '.chip-stack.demoted.stack-character7-seat1',
    '.chip-stack.demoted.stack-character8-seat1',
    '#pot'
  ];

  const animMap = new WeakMap();   // element -> current WAAPI animation
  const filterMap = new WeakMap();   // element -> original computed filter string

  function getTargets() {
    const set = new Set();
    SELECTORS.forEach(sel => {
      document.querySelectorAll(sel).forEach(el => { if (el instanceof HTMLElement) set.add(el); });
    });
    return Array.from(set);
  }

  /* ===== Helpers ===== */
  function cacheOriginalFilter(el) {
    if (!filterMap.has(el)) {
      const f = getComputedStyle(el).filter || 'none';
      filterMap.set(el, f);
      // Ensure inline filter is set to original to give us a stable base
      if (f && f !== 'none') el.style.filter = f;
      else el.style.removeProperty('filter');
    }
    return filterMap.get(el);
  }

  function cancelAnim(el) {
    const a = animMap.get(el);
    if (a) { try { a.cancel(); } catch (_) { } }
  }

  function readShowTimingFor(el) {
    const dDelay = parseFloat(el.dataset?.fadeDelay || '');
    const dDur = parseFloat(el.dataset?.fadeDuration || '');
    const delay = Number.isNaN(dDelay) ? SHOW_DELAY_S : Math.max(0, dDelay);
    const dur = Number.isNaN(dDur) ? SHOW_DURATION_S : Math.max(0.001, dDur);
    return { delay, dur, easing: 'ease' };
  }

  /* ===== SNAP (no animation) using filter opacity ===== */
  function snapHidden(el, hidden) {
    cancelAnim(el);
    const base = cacheOriginalFilter(el);
    if (hidden) {
      // Apply base + opacity(0)
      el.style.filter = (base && base !== 'none') ? (base + ' opacity(0)') : 'opacity(0)';
    } else {
      // Restore exactly the original filter
      if (base && base !== 'none') el.style.filter = base;
      else el.style.removeProperty('filter');
    }
  }

  /* ===== Animated show/hide (only on zoom toggle) ===== */
  function animateHide(el) {
    cancelAnim(el);
    const base = cacheOriginalFilter(el);
    // Start from current visual state; animate filter opacity to 0
    const from = getComputedStyle(el).filter || (base && base !== 'none' ? base : 'none');
    const a = el.animate(
      [{ filter: from }, { filter: (base && base !== 'none') ? (base + ' opacity(0)') : 'opacity(0)' }],
      { duration: 1, delay: 0, easing: 'linear', fill: 'forwards' }
    );
    animMap.set(el, a);
  }

  function animateShow(el) {
    cancelAnim(el);
    const base = cacheOriginalFilter(el);
    // Ensure we start visually at 0 without touching opacity/transition props
    const hiddenFilter = (base && base !== 'none') ? (base + ' opacity(0)') : 'opacity(0)';
    el.style.filter = hiddenFilter;

    // Two RAFs to let layout settle (avoids flash with parent visibility flips)
    requestAnimationFrame(() => requestAnimationFrame(() => {
      const { delay, dur, easing } = readShowTimingFor(el);
      const a = el.animate(
        [{ filter: hiddenFilter }, { filter: (base && base !== 'none') ? base : 'none' }],
        {
          duration: Math.max(1, dur * 1000),
          delay: Math.max(0, delay * 1000),
          easing,
          fill: 'forwards'
        }
      );
      animMap.set(el, a);
    }));
  }

  /* ===== State drivers ===== */
  function applyHiddenStateWithAnimation() {
    const hidden = VC.classList.contains('chars-hidden');
    const T = getTargets();
    if (hidden) T.forEach(animateHide);
    else T.forEach(animateShow);
  }

  function snapToCurrentStateNoAnim() {
    const hidden = VC.classList.contains('chars-hidden');
    const T = getTargets();
    T.forEach(el => snapHidden(el, hidden));
  }

  /* ===== Wire-up ===== */
  // Initial snap so first paint matches current state
  snapToCurrentStateNoAnim();

  // Animate ONLY when the hidden flag actually changes
  let prevHidden = VC.classList.contains('chars-hidden');
  const classObs = new MutationObserver(() => {
    const nowHidden = VC.classList.contains('chars-hidden');
    if (nowHidden === prevHidden) return;
    prevHidden = nowHidden;
    applyHiddenStateWithAnimation();
  });
  classObs.observe(VC, { attributes: true, attributeFilter: ['class'] });

  // On subtree churn (checkmarks etc.), keep filters consistent but do NOT animate
  const treeObs = new MutationObserver(() => {
    // Recache any new nodes' base filters and snap; do not alter timings/animations
    getTargets().forEach(el => cacheOriginalFilter(el));
    snapToCurrentStateNoAnim();
  });
  treeObs.observe(document.body, { childList: true, subtree: true });

  // Keep things tidy on resize without triggering fades
  window.addEventListener('resize', snapToCurrentStateNoAnim, { passive: true });
})();


/* ===== inline script 10 ===== */
(function () {
  const VIDEO_MAP = {
    morning: 'video/bg-day.mp4',
    day: 'video/bg-day.mp4',
    evening: 'video/bg-day.mp4',
    night: 'video/bg-night.mp4'
  };

  function toDecimal(timeStr) { const [h, m] = timeStr.split(':').map(Number); return h + (m || 0) / 60; }

  const RANGES = [
    { name: 'morning', start: toDecimal('05:00'), end: toDecimal('06:00') },
    { name: 'day', start: toDecimal('06:00'), end: toDecimal('17:00') },
    { name: 'evening', start: toDecimal('17:00'), end: toDecimal('18:00') },
    { name: 'night', start: toDecimal('18:00'), end: toDecimal('24:00') },
    { name: 'night', start: toDecimal('00:00'), end: toDecimal('05:00') }
  ];

  const STORAGE_PREFIX = 'bgOffset:';
  const CLASSES = ['bg-morning', 'bg-day', 'bg-evening', 'bg-night'];

  const bgWrap = document.querySelector('.bg-stack');
  const video = document.getElementById('backgroundVideo');

  function getLocalHM(tz) {
    const now = new Date();
    const h = new Intl.DateTimeFormat('en-US', { timeZone: tz, hour: 'numeric', hour12: false }).format(now);
    const m = new Intl.DateTimeFormat('en-US', { timeZone: tz, minute: 'numeric', hour12: false }).format(now);
    return { hour: Number(h), minute: Number(m) };
  }

  function periodFor(hourDec) {
    for (const r of RANGES) if (r.start <= hourDec && hourDec < r.end) return r.name;
    return 'night';
  }

  function minutesUntilBoundary(hour, minute) {
    const hourDec = hour + minute / 60;
    let end = null;
    for (const r of RANGES) {
      if (r.start <= hourDec && hourDec < r.end) { end = r.end; break; }
    }
    if (end === null) end = (hourDec >= 21 ? 24 : 5);
    const nowMin = (hour * 60) + minute;
    const endMin = Math.round(end * 60);
    let diff = (endMin - nowMin);
    if (diff <= 0) diff += 1440;
    return diff;
  }

  function storageKey(period) { return STORAGE_PREFIX + period; }
  let __storageOK = null;
  function storageOK() {
    if (__storageOK !== null) return __storageOK;
    try {
      const k = '__t__' + Math.random().toString(16).slice(2);
      localStorage.setItem(k, '1');
      localStorage.removeItem(k);
      __storageOK = true;
    } catch (_) {
      __storageOK = false;
    }
    return __storageOK;
  }

  function saveOffset(period) {
    if (!storageOK()) return;
    try {
      const t = Math.max(0, video.currentTime || 0);
      localStorage.setItem(storageKey(period), String(t));
    } catch (_) { }
  }

  function readOffset(period) {
    if (!storageOK()) return 0;
    try {
      const v = localStorage.getItem(storageKey(period));
      return v ? Math.max(0, parseFloat(v)) : 0;
    } catch (_) { return 0; }
  }


  let saveTimer = null;
  function startSaving(period) {
    stopSaving();
    saveTimer = setInterval(() => saveOffset(period), 2000);
  }
  function stopSaving() {
    if (saveTimer) { clearInterval(saveTimer); saveTimer = null; }
  }

  async function fadeBlack(on) {
    const fader = document.getElementById('bgFader');
    if (!fader) return Promise.resolve();
    return new Promise(resolve => {
      const dur = parseFloat(getComputedStyle(fader).transitionDuration || '0') * 1000;
      const done = () => resolve();
      const handler = (e) => { if (e.target === fader) { fader.removeEventListener('transitionend', handler); done(); } };
      if (dur > 0) fader.addEventListener('transitionend', handler, { once: true });
      requestAnimationFrame(() => {
        fader.classList.toggle('is-on', on);
        if (dur === 0) done();
      });
      if (dur > 0) setTimeout(done, dur + 50);
    });
  }

  async function applyPeriod(period) {
    bgWrap.classList.remove(...CLASSES);
    bgWrap.classList.add('bg-' + period);

    if (video.dataset.current === period) return;

    const src = VIDEO_MAP[period];

    const desiredOffset = readOffset(period);
    let pendingSeek = desiredOffset;

    await fadeBlack(true);

    try { video.pause(); } catch (_) { }
    video.src = src;
    video.dataset.current = period;
    video.autoplay = true;
    video.muted = true;
    video.playsInline = true;

    await new Promise(res => {
      const onMeta = () => {
        video.removeEventListener('loadedmetadata', onMeta);
        const dur = isFinite(video.duration) ? video.duration : 0;
        let seekTo = pendingSeek || 0;
        if (dur > 0) seekTo = seekTo % dur;
        try { if (!isNaN(seekTo)) video.currentTime = seekTo; } catch (_) { }
        const p = video.play && video.play();
        p && p.finally ? p.finally(res) : res();
      };
      video.addEventListener('loadedmetadata', onMeta, { once: true });
      video.load();
    });

    await fadeBlack(false);

    video.onplay = () => startSaving(period);
    video.onpause = () => saveOffset(period);
    video.onended = () => saveOffset(period);

    const saveNow = () => saveOffset(period);
    document.addEventListener('visibilitychange', saveNow);
    window.addEventListener('pagehide', saveNow, { once: true });
  }

  let boundaryTimer = 0;
  function scheduleNextSwap(tz) {
    if (boundaryTimer) clearTimeout(boundaryTimer);
    const { hour, minute } = getLocalHM(tz);
    const mins = minutesUntilBoundary(hour, minute);
    boundaryTimer = setTimeout(() => swapByTime(tz), mins * 60 * 1000);
  }

  function swapByTime(tz) {
    const { hour, minute } = getLocalHM(tz);
    const period = periodFor(hour + minute / 60);
    applyPeriod(period);
    scheduleNextSwap(tz);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
    swapByTime(tz);

    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) swapByTime(tz);
    });

    setInterval(() => swapByTime(tz), 60 * 1000);
  });
})();

/* ===== inline script 11 (LIVE BUTTON + CLEAN CLOSE) ===== */
(function () {
  const CHARACTER = 1;
  const SEAT = 1;
  const TARGET_ID = `video_character${CHARACTER}_seat${SEAT}`;
  const BTN_SELECTOR = '#btnLiveC1';

  let liveStream = null;
  let pipEl = null;

  function findOverlayForVideoId(videoId) {
    return document.getElementById('overlay_' + videoId);
  }

  function stopLiveTracks() {
    if (liveStream) {
      try { liveStream.getTracks().forEach(t => t.stop()); } catch (_) { }
      liveStream = null;
    }
  }

  function ensureOverlayExists(videoEl) {
    if (!window.__overlayHelpers__ || !window.__overlayHelpers__.makeOverlay) return null;
    window.__overlayHelpers__.makeOverlay(videoEl);
    return findOverlayForVideoId(videoEl.id);
  }

  function setBtnActive(active) {
    const btn = document.querySelector(BTN_SELECTOR);
    if (!btn) return;
    btn.classList.toggle('active', !!active);
    btn.setAttribute('aria-pressed', active ? 'true' : 'false');
  }

  function removePipAndMirror() {
    // Remove mirror (created by your "unclip PIP" script)
    try {
      if (pipEl) {
        const mirrorId = pipEl.getAttribute('data-pip-mirror-id');
        if (mirrorId) {
          const mirror = document.getElementById(mirrorId);
          if (mirror) {
            try { mirror.pause(); } catch (_) { }
            try { mirror.srcObject = null; } catch (_) { }
            mirror.remove();
          }
        }
      }
    } catch (_) { }

    // Remove the real pip
    try {
      if (pipEl && pipEl.isConnected) {
        try { pipEl.pause(); } catch (_) { }
        try { pipEl.srcObject = null; } catch (_) { }
        pipEl.remove();
      }
    } catch (_) { }

    pipEl = null;
  }

  function waitForMainVideo(maxMs = 1500) {
    return new Promise(resolve => {
      const start = Date.now();
      (function tick() {
        const v = document.getElementById(TARGET_ID);
        if (v) return resolve(v);
        if (Date.now() - start >= maxMs) return resolve(null);
        requestAnimationFrame(tick);
      })();
    });
  }

  async function toggleLivePip() {
    // IMPORTANT: do NOT preventDefault/stopPropagation
    // so your live-game button keeps its normal behavior.

    // Toggle OFF
    if (pipEl && pipEl.isConnected) {
      removePipAndMirror();   // <-- KEY FIX (prevents black box)
      stopLiveTracks();
      setBtnActive(false);
      return;
    }

    // Toggle ON
    const mainVideo = await waitForMainVideo(2000);
    if (!mainVideo) return;

    const overlay = ensureOverlayExists(mainVideo) || findOverlayForVideoId(TARGET_ID);
    if (!overlay) return;

    try {
      if (!liveStream) {
        liveStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
      }

      const v = document.createElement('video');
      v.className = 'live-pip';
      v.autoplay = true;
      v.muted = true;
      v.playsInline = true;
      v.controls = false;
      v.srcObject = liveStream;

      const tb = overlay.querySelector('.text-box');
      (tb || overlay).appendChild(v);
      pipEl = v;

      if (window.__overlayHelpers__ && window.__overlayHelpers__.repositionAllOverlays) {
        window.__overlayHelpers__.repositionAllOverlays();
      }

      setBtnActive(true);

    } catch (err) {
      removePipAndMirror();
      stopLiveTracks();
      setBtnActive(false);
      alert('Could not access camera. Check permissions and try again.');
    }
  }

  const btn = document.querySelector(BTN_SELECTOR);
  if (btn) btn.addEventListener('click', toggleLivePip, false);

  window.__livePipC1__ = { toggle: toggleLivePip };
})();


/* ===== inline script 12 ===== */
(function () {
  const VC = document.getElementById('videoContainer');
  const GHOST = 'chip-stack-ghost';
  const PINNED_SEATS = new Set([1, 2, 9]); // seats that should NOT be demoted

  // overlay id pattern: overlay_video_character{ch}_seat{seat}
  function isPinnedSeat(overlayId) {
    const m = /overlay_video_character(\d+)_seat(\d+)/.exec(overlayId || '');
    if (!m) return false;
    const seat = Number(m[1]); // in your overlay IDs, {ch} maps to the seat/character index
    return PINNED_SEATS.has(seat);
  }

  function ensureGhost(overlay, realStack) {
    let ghost = overlay.querySelector('.' + GHOST);
    if (!ghost) {
      ghost = document.createElement('img');
      ghost.className = `${GHOST} chip-stack`;
      ghost.alt = 'ghost';
      ghost.style.visibility = 'hidden';
      ghost.style.pointerEvents = 'none';
      ghost.style.opacity = '0';
      // copy seat-specific classes so your CSS anchors still apply
      realStack.className.split(' ').forEach(c => {
        if (c && c !== 'chip-stack' && !ghost.classList.contains(c)) ghost.classList.add(c);
      });
      overlay.appendChild(ghost);
    }
    ghost.src = realStack.src;
    return ghost;
  }

  function promoteIfPinned(overlay, stack) {
    // If a stack was previously demoted but its seat is pinned now, move it back into the overlay.
    if (stack.classList.contains('demoted')) {
      overlay.appendChild(stack);
      stack.classList.remove('demoted');
      stack.style.left = stack.style.top = stack.style.translate = '';
    }
  }

  function demoteIfNotPinned(overlay, stack) {
    if (!stack.classList.contains('demoted')) {
      ensureGhost(overlay, stack);
      stack.classList.add('demoted');
      stack.style.position = 'absolute';
      stack.style.pointerEvents = 'none';
      VC.appendChild(stack);
    }
  }

  function demoteStacksOnce() {
    document.querySelectorAll('.overlay-box').forEach(overlay => {
      const stack = overlay.querySelector('.chip-stack:not(.' + GHOST + ')');
      if (!stack) return;
      if (isPinnedSeat(overlay.id)) {
        promoteIfPinned(overlay, stack); // keep seats 1, 2, 9 above
      } else {
        demoteIfNotPinned(overlay, stack); // everyone else goes under videos
      }
    });
  }

  function syncDemotedPositions() {
    const vcRect = VC.getBoundingClientRect();
    document.querySelectorAll('.chip-stack.demoted').forEach(real => {
      const ovId = real.id.replace(/^stack_/, 'overlay_');
      const overlay = document.getElementById(ovId);
      if (!overlay) return;
      const ghost = overlay.querySelector('.' + GHOST);
      if (!ghost) return;
      const r = ghost.getBoundingClientRect();
      real.style.left = (r.left - vcRect.left) + 'px';
      real.style.top = (r.top - vcRect.top) + 'px';
    });
  }

  function tick() {
    demoteStacksOnce();
    syncDemotedPositions();
    requestAnimationFrame(tick);
  }
  requestAnimationFrame(tick);

  // React to DOM changes (seat toggles/loads)
  const mo = new MutationObserver(demoteStacksOnce);
  mo.observe(document.body, { childList: true, subtree: true });

  // Cooperate with overlay helpers if present.
  const oh = (window.__overlayHelpers__ = window.__overlayHelpers__ || {});
  const mk = oh.makeOverlay?.bind(oh);
  oh.makeOverlay = function (v) { if (mk) mk(v); demoteStacksOnce(); };
  const rp = oh.repositionAllOverlays?.bind(oh);
  oh.repositionAllOverlays = function () { if (rp) rp(); syncDemotedPositions(); };
})();


/* ===== inline script 13 ===== */
(function () {
  const btn = document.getElementById('menuButton');
  const root = document.documentElement;
  if (!btn) return;

  function toggleMenu(force) {
    document.body.classList.toggle('menu-open', force ?? !document.body.classList.contains('menu-open'));
  }

  btn.addEventListener('click', () => toggleMenu());

  // Close on ESC
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') toggleMenu(false);
  });
})();


/* ===== inline script 14 ===== */
(function () {
  const btn = document.getElementById('menuButton');
  let mouseTimer;
  const hideDelay = 2000; // ms of no movement before hiding

  function showButton() {
    if (!document.body.classList.contains('menu-open')) {
      btn.style.opacity = '1';
      btn.style.pointerEvents = 'auto';
    }
  }

  function hideButton() {
    if (!document.body.classList.contains('menu-open')) {
      btn.style.opacity = '0';
      btn.style.pointerEvents = 'none';
    }
  }

  // Track mouse movement anywhere on the window
  window.addEventListener('mousemove', () => {
    showButton();
    clearTimeout(mouseTimer);
    mouseTimer = setTimeout(hideButton, hideDelay);
  });

  // Keep the button visible while menu is open
  const observer = new MutationObserver(() => {
    if (document.body.classList.contains('menu-open')) {
      btn.style.opacity = '1';
      btn.style.pointerEvents = 'auto';
    }
  });
  observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
})();


/* ===== inline script 15 ===== */
(function () {
  const menu = document.getElementById('lobbyMenu');
  if (!menu) return;

  const btns = menu.querySelectorAll('.lobby-btn');

  function setActive(btn) {
    // toggle button visuals + ARIA
    btns.forEach(b => {
      const on = (b === btn);
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });

    // collect all targets from buttons
    const targets = Array.from(btns)
      .map(b => b.dataset.target)
      .filter(Boolean);

    // hide all targets, then show the one for the clicked button
    targets.forEach(sel => {
      const el = document.querySelector(sel);
      if (el) el.classList.add('is-hidden');
    });

    const show = document.querySelector(btn.dataset.target);
    if (show) show.classList.remove('is-hidden');
  }

  // click handlers
  btns.forEach(b => b.addEventListener('click', () => setActive(b)));

  // optional: ensure initial state matches any pre-set is-active
  const initial = Array.from(btns).find(b => b.classList.contains('is-active')) || btns[0];
  if (initial) setActive(initial);
})();


/* ===== inline script 16 ===== */
(function () {
  const header = document.querySelector('.landing-header');
  if (!header) return;
  const menuBtn = header.querySelector('.menu-icon');
  const nav = header.querySelector('.site-nav');
  if (!menuBtn || !nav) return;

  function toggleNav(force) {
    const open = force ?? !header.classList.contains('menu-visible');
    header.classList.toggle('menu-visible', open);
  }
  menuBtn.addEventListener('click', () => toggleNav());
  menuBtn.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggleNav(); }
  });
  document.addEventListener('click', (e) => {
    if (!header.contains(e.target)) toggleNav(false);
  });
  window.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleNav(false); });
})();


/* ===== inline script 17 ===== */
document.addEventListener('DOMContentLoaded', () => {
  const box = document.getElementById('checkboxContainer');

  // Add a simple toggle if one isn't already there
  if (!document.getElementById('toggleOpenSeats')) {
    const label = document.createElement('label');
    label.style.color = '#fff';
    label.style.display = 'inline-flex';
    label.style.alignItems = 'center';
    label.style.gap = '6px';

    const cb = document.createElement('input');
    cb.type = 'checkbox';
    cb.id = 'toggleOpenSeats';
    cb.checked = true; // checked = hide OPEN (current behavior)

    label.append(cb, document.createTextNode(' Hide OPEN on empty seats'));
    box.appendChild(label);
  }

  const toggle = document.getElementById('toggleOpenSeats');

  function syncOpenSeatMode() {
    // Unchecked => show OPEN on empty seats
    document.body.classList.toggle('show-open-seats', !toggle.checked);
  }

  toggle.addEventListener('change', syncOpenSeatMode);
  syncOpenSeatMode(); // set initial state
});


/* ===== inline script 18 ===== */
/* ================================================================
   EMPTY SEATS AT START (ghost-seeded, seats 1,2,3,7,8,9 only)
   - Fixes seat 2 sizing by giving ghosts an intrinsic portrait size
   - Excludes seats 4,5,6 from startup
   ================================================================ */
(() => {
  const VC = document.getElementById('videoContainer');
  if (!VC) return;

  // Only seed these seats at boot:
  const SEATS = [1, 2, 3, 7, 8, 9];

  const vidId = s => `video_character${s}_seat1`;
  const vidCls = s => `character${s}-seat1`;
  const hasReal = s => !!document.getElementById(vidId(s));

  function readyHelpers(cb) {
    if (
      window.__overlayHelpers__ &&
      typeof window.__overlayHelpers__.makeOverlay === 'function' &&
      typeof window.__overlayHelpers__.showEmptySeatForId === 'function' &&
      typeof window.__overlayHelpers__.clearEmptySeatForId === 'function'
    ) { cb(); }
    else requestAnimationFrame(() => readyHelpers(cb));
  }

  // Create a temporary ghost video so CSS seat rules apply,
  // then let overlay helper compute & place the overlay.
  function seedEmptySeat(seat) {
    if (hasReal(seat)) return;

    const ov = document.getElementById(`overlay_${vidId(seat)}`);
    if (ov && ov.dataset.empty === '1') return;

    const ghost = document.createElement('video');
    ghost.id = vidId(seat);
    ghost.className = vidCls(seat);
    ghost.setAttribute('data-ghost', '1');

    // 👇 Critical: give the ghost a portrait intrinsic size so
    // CSS like width:19%; height:auto has real dimensions.
    ghost.setAttribute('width', '1080');   // intrinsic width
    ghost.setAttribute('height', '1920');  // intrinsic height
    ghost.style.aspectRatio = '9 / 16';
    ghost.style.position = 'absolute';
    ghost.style.opacity = '0';
    ghost.style.pointerEvents = 'none';

    VC.appendChild(ghost);

    // Position overlay relative to the ghost, then mark empty
    window.__overlayHelpers__.makeOverlay(ghost);
    window.__overlayHelpers__.showEmptySeatForId(ghost.id);

    // Remove ghost; overlay stays put
    ghost.remove();
  }

  function syncAll() {
    document.body.classList.add('show-open-seats');
    SEATS.forEach(seat => {
      if (hasReal(seat)) {
        window.__overlayHelpers__.clearEmptySeatForId(vidId(seat));
      } else {
        seedEmptySeat(seat);
      }
    });
  }

  function onDomChange() {
    SEATS.forEach(seat => {
      if (hasReal(seat)) {
        window.__overlayHelpers__.clearEmptySeatForId(vidId(seat));
      } else {
        seedEmptySeat(seat);
      }
    });
  }

  function onOpenClick(e) {
    const box = e.target.closest('.overlay-box[data-empty="1"]');
    if (!box) return;
    const m = /^overlay_video_character(\d+)_seat1$/.exec(box.id);
    if (!m) return;
    const seat = parseInt(m[1], 10);

    // Only respond for seats we manage at boot
    if (!SEATS.includes(seat)) return;

    const cb = document.querySelector(
      `#checkboxContainer input[type="checkbox"][data-seat="${seat}"],` +
      `#checkboxContainer input[type="checkbox"][value="${seat}"]`
    );
    if (cb && !cb.checked) {
      cb.checked = true;
      cb.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  readyHelpers(() => {
    // Initial boot
    syncAll();

    // Watch only for real video mount/unmount under VC
    const mo = new MutationObserver(onDomChange);
    mo.observe(VC, { childList: true, subtree: true });

    // Claim on click
    VC.addEventListener('click', onOpenClick, { passive: true });

    // Harmless resync on resize
    addEventListener('resize', onDomChange, { passive: true });
  });
})();


/* ===== inline script 19 ===== */
(function () {
  const menu = document.getElementById('lobbyMenu');
  if (!menu) return;

  // Create one reusable hover card appended to <body>
  const card = document.createElement('div');
  card.className = 'lobby-hover-card';
  card.innerHTML = `<div class="lobby-hover-card__title"></div>`;
  document.body.appendChild(card);

  let mediaEl = null;     // <img> or <video> inserted into card
  let activeBtn = null;   // currently hovered button
  let hideTimer = null;

  function ensureMediaEl(tag) {
    if (mediaEl && mediaEl.tagName.toLowerCase() === tag) return mediaEl;
    if (mediaEl) mediaEl.remove();
    mediaEl = document.createElement(tag);
    mediaEl.className = 'lobby-hover-card__media';
    if (tag === 'video') {
      mediaEl.autoplay = true;
      mediaEl.loop = true;
      mediaEl.muted = true;
      mediaEl.playsInline = true;
      mediaEl.setAttribute('playsinline', '');
      mediaEl.setAttribute('muted', '');
      mediaEl.setAttribute('autoplay', '');
      mediaEl.setAttribute('loop', '');
    }
    card.insertBefore(mediaEl, card.firstChild);
    return mediaEl;
  }

  function getTarget(btn) {
    const sel = btn.getAttribute('data-target');
    if (!sel) return null;
    return document.querySelector(sel);
  }

  function getMediaFromTarget(target) {
    if (!target) return null;
    // Accept either direct <img>/<video> child or nested inside
    const vid = target.querySelector('video');
    const img = target.querySelector('img');
    if (vid && (vid.currentSrc || vid.src)) {
      return { type: 'video', src: vid.currentSrc || vid.src, poster: vid.getAttribute('poster') || '' };
    }
    if (img && img.src) {
      return { type: 'image', src: img.src };
    }
    // Also accept data attributes if you store them that way
    const dataSrc = target.getAttribute('data-media');
    const dataType = target.getAttribute('data-type');
    if (dataSrc) {
      return { type: (dataType || 'image').toLowerCase(), src: dataSrc };
    }
    return null;
  }

  function getTitleFromTarget(target, fallback) {
    return target?.getAttribute('data-title')
      || target?.getAttribute('aria-label')
      || (target?.textContent || '').trim()
      || fallback;
  }

  function positionCard(btn) {
    const rect = btn.getBoundingClientRect();
    const width = Math.round(rect.width);

    // Set card width to exact button width
    card.style.width = width + 'px';

    // Compute height from CSS aspect ratio (16/9 default)
    const ratio = 16 / 9;
    const height = width / ratio;

    const gap = 10;
    let left = Math.round(rect.left);
    let top = Math.round(rect.top - height - gap);

    // Keep on screen horizontally
    left = Math.max(8, Math.min(left, window.innerWidth - width - 8));
    // Flip below if no space above
    if (top < 8) top = Math.round(rect.bottom + gap);

    card.style.left = left + 'px';
    card.style.top = top + 'px';
  }

  function showCard(btn) {
    const target = getTarget(btn);
    const mediaInfo = getMediaFromTarget(target);
    if (!mediaInfo || !mediaInfo.src) return;

    activeBtn = btn;
    clearTimeout(hideTimer);

    // Title
    const titleText = getTitleFromTarget(target, btn.textContent.trim());
    card.querySelector('.lobby-hover-card__title').textContent = titleText;

    // Media
    if (mediaInfo.type === 'video') {
      const v = ensureMediaEl('video');
      if (v.src !== mediaInfo.src) {
        v.src = mediaInfo.src;
        if (mediaInfo.poster) v.poster = mediaInfo.poster;
      }
      v.currentTime = 0;
      v.play().catch(() => { });
    } else {
      const i = ensureMediaEl('img');
      if (i.src !== mediaInfo.src) i.src = mediaInfo.src;
    }

    positionCard(btn);
    requestAnimationFrame(() => card.classList.add('is-on'));
  }

  function hideCard(immediate = false) {
    clearTimeout(hideTimer);
    const doHide = () => {
      card.classList.remove('is-on');
      activeBtn = null;
    };
    if (immediate) doHide();
    else hideTimer = setTimeout(doHide, 60);
  }

  // Events — mouse + keyboard focus
  menu.addEventListener('mouseenter', onEnter, true);
  menu.addEventListener('mouseleave', onLeave, true);
  menu.addEventListener('focusin', onEnter, true);
  menu.addEventListener('focusout', onLeave, true);

  function onEnter(e) {
    const btn = e.target.closest('.lobby-btn');
    if (!btn) return;
    showCard(btn);
  }
  function onLeave(e) {
    const btn = e.target.closest('.lobby-btn');
    if (!btn) return;
    if (btn === activeBtn) hideCard();
  }

  // Keep positions correct
  window.addEventListener('scroll', () => hideCard(true), { passive: true });
  window.addEventListener('resize', () => hideCard(true));
})();


/* ===== inline script 20 ===== */
document.addEventListener('DOMContentLoaded', function () {
  // --- Room 1 baseline anchors (existing DOM)
  const r1 = {
    bgVideo: document.getElementById('backgroundVideo'),
    roomImg: document.querySelector('img.room'),
    tableImg: document.querySelector('img.tabletop'),
    chairs: document.getElementById('chairsLayer'),
    videos: document.getElementById('videoContainer'),
    zoom: document.getElementById('zoomArea'),
    pot: document.getElementById('pot')
  };

  // --- Extra room sections (appended above)
  const sections = {
    2: document.getElementById('room2Section'),
    3: document.getElementById('room3Section'),
    4: document.getElementById('room4Section')
  };
  const videos = {
    1: r1.bgVideo,
    2: document.getElementById('backgroundVideo_2'),
    3: document.getElementById('backgroundVideo_3'),
    4: document.getElementById('backgroundVideo_4')
  };

  // Map lobby buttons ($5-10, etc.) => #tableMediaX => X
  const lobbyBtns = document.querySelectorAll('.lobby-btn');
  const parseNum = (sel) => { const m = (sel || '').match(/\d+$/); return m ? parseInt(m[0], 10) : 1; };

  // Utility: load a src only when needed, with fallback to Room 1’s current media if missing
  function safeSet(el, src, fallback) {
    if (!el) return;
    if (!src) { el.removeAttribute('src'); return; }
    // apply and probe; if it errors, use fallback
    const tag = el.tagName.toLowerCase();
    const onErr = () => {
      if (fallback) { el.src = fallback; }
      el.dispatchEvent(new Event('errorHandled'));
    };
    el.addEventListener('error', onErr, { once: true });
    el.src = src;
    if (tag === 'video' && el.load) el.load();
  }

  function hideRoom1(hide) {
    const v = hide ? 'none' : '';
    if (r1.bgVideo) r1.bgVideo.style.display = v;
    if (r1.roomImg) r1.roomImg.style.display = v;
    if (r1.tableImg) r1.tableImg.style.display = v;
    if (r1.chairs) r1.chairs.style.display = v;
    if (r1.videos) r1.videos.style.display = v;
    if (r1.zoom) r1.zoom.style.display = v;
    if (r1.pot) r1.pot.style.display = v;
  }

  function pauseAllExcept(n) {
    for (let i = 1; i <= 4; i++) {
      const v = videos[i];
      if (!v) continue;
      const hasSrc = v.getAttribute('src') || v.currentSrc;
      try { (i === n && hasSrc) ? v.play() : v.pause(); } catch (e) { }
    }
  }

  function activateRoom(n) {
    // Deactivate all appended sections
    for (const k of [2, 3, 4]) {
      const s = sections[k];
      if (s) { s.classList.remove('active'); s.setAttribute('aria-hidden', 'true'); }
    }

    if (n === 1) {
      // Show Room 1
      hideRoom1(false);
      pauseAllExcept(1);
      return;
    }

    const s = sections[n];
    if (!s) { hideRoom1(false); pauseAllExcept(1); return; }

    // Lazy-apply sources from data-* only when first activated
    const bgSrc = s.getAttribute('data-bg');
    const roomSrc = s.getAttribute('data-room');
    const tableSrc = s.getAttribute('data-table');

    const v = videos[n];
    const imgRoom = s.querySelector('img.room');
    const imgTable = s.querySelector('img.tabletop');

    // Use current Room 1 media as fallback (guaranteed existing)
    const fbVideo = r1.bgVideo ? (r1.bgVideo.getAttribute('src') || r1.bgVideo.currentSrc) : '';
    const fbRoom = r1.roomImg ? r1.roomImg.getAttribute('src') : '';
    const fbTable = r1.tableImg ? r1.tableImg.getAttribute('src') : '';

    // Only set sources if not already set (prevents repeat 404 spam)
    if (v && !v.getAttribute('src')) safeSet(v, bgSrc, fbVideo);
    if (imgRoom && !imgRoom.getAttribute('src')) safeSet(imgRoom, roomSrc, fbRoom);
    if (imgTable && !imgTable.getAttribute('src')) safeSet(imgTable, tableSrc, fbTable);

    // Show this section; hide Room 1
    hideRoom1(true);
    s.classList.add('active');
    s.setAttribute('aria-hidden', 'false');

    // Play/pause
    pauseAllExcept(n);
  }

  // Lobby click wiring
  function setActiveBtn(btn) {
    const sibs = btn.parentElement.querySelectorAll('.lobby-btn');
    sibs.forEach(b => {
      const on = (b === btn);
      b.classList.toggle('is-active', on);
      b.setAttribute('aria-selected', on ? 'true' : 'false');
    });
  }
  lobbyBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault?.();
      setActiveBtn(btn);
      const n = parseNum(btn.getAttribute('data-target'));
      activateRoom(n);
    });
  });

  // Init: respect current .is-active button
  const initial = document.querySelector('.lobby-btn.is-active') || lobbyBtns[0];
  activateRoom(parseNum(initial?.getAttribute('data-target')) || 1);

  // Optional: Game Mode toggle
  // NOTE: Must be selected by explicit id.
  // Using '#controls button:last-child' broke once the Timeout button was added.
  const gameModeBtn = document.getElementById('gameModeBtn');
  gameModeBtn?.addEventListener('click', () => {
    document.body.classList.toggle('game-mode-active');
  });

  // Hide broken small icons (e.g., camera.png) without console spam
  document.querySelectorAll('img').forEach(im => {
    im.addEventListener('error', () => { im.style.display = 'none'; }, { once: true });
  });
});


/* ===== inline script 21 ===== */
/* ================================================================
   Keep character layer visible across rooms
   - No DOM moves. No CSS changes. No "OPEN" badges.
   - Checkboxes keep controlling your original #videoContainer.
   - Rooms 2–4: no extra UI injected.
   ================================================================= */
(function () {
  // Seats present on the table (kept for parity; unused now)
  const SEATS = [1, 2, 3, 4, 5, 6, 7, 8, 9];
  const SEAT_VIEW = 1; // matches your existing id pattern: video_character{N}_seat1

  // 1) Keep character layer ALWAYS visible (so characters appear when toggled).
  //    If any prior script hides it on room switch, force it back on.
  const chairsLayer = document.getElementById('chairsLayer');
  const videoContainer = document.getElementById('videoContainer');

  function keepCharacterLayersVisible() {
    if (videoContainer) videoContainer.style.display = '';
    if (chairsLayer) chairsLayer.style.display = '';
  }

  // 2) Helpers (kept minimal; no OPEN logic)
  function activeRoomNumber() {
    const btn = document.querySelector('.lobby-btn.is-active');
    const id = btn?.getAttribute('data-target') || '';
    const m = id.match(/\d+$/);
    return m ? parseInt(m[0], 10) : 1;
  }

  // 3) Wire checkbox changes (do NOT interfere with your existing handlers)
  document.addEventListener('change', function (ev) {
    const t = ev.target;
    if (t && t.matches('input.seatCheckbox')) {
      // Make sure character layers remain visible after toggles
      keepCharacterLayersVisible();
    }
  }, true);

  // 4) On lobby switch, just keep character layers visible
  document.querySelectorAll('.lobby-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      // Let your existing room-switcher finish first
      setTimeout(() => {
        keepCharacterLayersVisible();
        // activeRoomNumber() is intentionally unused here; we just ensure visibility
      }, 0);
    });
  });

  // 5) First run
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', keepCharacterLayersVisible);
  } else {
    keepCharacterLayersVisible();
  }
})();


/* ===== inline script 22 ===== */
/* ==============================================================
   ROOMS 2–4: Pot alignment + chip-stack settle (safe & minimal)
   - No CSS/DOM moves, no play()/pause() calls.
   - Mirrors Room 1's on-screen pot placement using RELATIVE offsets.
   - Lifts chip stacks above the table and forces a layout tick so they settle.
   ============================================================== */
(function () {
  // ---- helpers ----
  function activeRoom() {
    const a = document.querySelector('.lobby-btn.is-active');
    const t = a ? a.getAttribute('data-target') || '' : '';
    const m = t.match(/\d+$/);
    return m ? parseInt(m[0], 10) : 1;
  }
  function r1Refs() {
    return { pot: document.getElementById('pot'), zoom: document.getElementById('zoomArea') };
  }
  function rNRefs(n) {
    return {
      pot: document.getElementById('pot_' + n),
      zoom: document.getElementById('zoomArea_' + n),
      vc: document.getElementById('videoContainer_' + n)
    };
  }

  // Measure Room 1 → get RELATIVE offsets (pot vs zoom center/right)
  function getRoom1Delta() {
    const { pot, zoom } = r1Refs();
    if (!pot || !zoom) return null;
    const pr = pot.getBoundingClientRect();
    const zr = zoom.getBoundingClientRect();
    return {
      dx: pr.left - zr.right,                 // horizontal delta from zoom right edge
      dy: pr.top - (zr.top + zr.height / 2),   // vertical delta from zoom vertical center
      width: getComputedStyle(pot).width || '120px',
      z: getComputedStyle(pot).zIndex || '30',
      transform: getComputedStyle(pot).transform
    };
  }

  // Apply Room 1’s relative offsets to Room N
  function placePotForRoom(n, base) {
    if (n === 1 || !base) return;
    const { pot, zoom } = rNRefs(n);
    if (!pot || !zoom) return;

    const zr = zoom.getBoundingClientRect();
    const left = zr.right + base.dx;
    const top = (zr.top + zr.height / 2) + base.dy;

    pot.style.position = 'fixed';
    pot.style.left = left + 'px';
    pot.style.top = top + 'px';
    pot.style.width = base.width;
    pot.style.zIndex = base.z;
    pot.style.pointerEvents = 'none';
    pot.style.transform = (base.transform && base.transform !== 'none') ? base.transform : 'translateY(-50%)';
    pot.style.visibility = 'visible';
    pot.style.display = 'block';
  }

  // Only the active room pot should be visible
  function showOnlyPot(room) {
    [2, 3, 4].forEach(n => {
      const p = document.getElementById('pot_' + n);
      if (!p) return;
      p.style.display = (n === room) ? 'block' : 'none';
      p.style.visibility = (n === room) ? 'visible' : 'hidden';
    });
  }

  // Lift chip stacks in rooms 2–4 above the table and “settle” transforms
  function settleChipStacks(vc) {
    if (!vc) return;
    vc.querySelectorAll('.chips, .chip-stack, .stack, .bet, .chips-layer, [data-role="chips"]').forEach(el => {
      if (!el.style.position) el.style.position = 'absolute';
      el.style.zIndex = '22';       // table ~10, videos ~20/21, overlays 200; this sits above table
      el.style.pointerEvents = 'none';
    });
    // force layout so transforms snap in the now-visible section
    vc.offsetWidth; requestAnimationFrame(() => { vc.offsetHeight; });
  }

  function updateForActiveRoom() {
    const r = activeRoom();
    const base = getRoom1Delta();   // always mirror current Room 1 relation
    if (r === 1) { showOnlyPot(-1); return; }

    showOnlyPot(r);
    requestAnimationFrame(() => {
      placePotForRoom(r, base);
      const { vc } = rNRefs(r);
      settleChipStacks(vc);
    });
  }

  // init + events
  const run = () => requestAnimationFrame(updateForActiveRoom);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', run);
  else run();

  // after your room-toggle finishes
  document.querySelectorAll('.lobby-btn').forEach(btn => btn.addEventListener('click', () => setTimeout(run, 0)));
  // keep aligned on resize
  window.addEventListener('resize', run);
})();


/* ===== inline script 23 ===== */
/* Force a custom playback speed for Room 3's background video */
(function setRoom3BgSpeed() {
  const RATE = 1; // <-- your desired speed
  const v = document.getElementById('backgroundVideo_3');
  if (!v) return;

  // Apply both default and current rate
  function applyRate() {
    if (!v) return;
    v.defaultPlaybackRate = RATE;
    if (v.playbackRate !== RATE) v.playbackRate = RATE;
  }

  // Re-apply on common lifecycle points where the browser or your code might reset it
  ['loadedmetadata', 'loadeddata', 'canplay', 'play', 'playing', 'ratechange', 'seeking', 'seeked'].forEach(ev =>
    v.addEventListener(ev, applyRate, { passive: true })
  );

  // If your code swaps the source (common with data-bg loaders), re-apply
  new MutationObserver(muts => {
    for (const m of muts) {
      if (m.type === 'attributes' && (m.attributeName === 'src' || m.attributeName === 'srcObject')) {
        applyRate();
      }
    }
  }).observe(v, { attributes: true, attributeFilter: ['src', 'srcObject'] });

  // If your room toggle adds/removes .active on sections, set rate when room 3 is shown
  const room3 = document.getElementById('room3Section');
  if (room3) {
    new MutationObserver(() => {
      if (room3.classList.contains('active')) applyRate();
    }).observe(room3, { attributes: true, attributeFilter: ['class'] });
  }

  // Initial pass (covers cases where the video is already ready)
  applyRate();
})();


/* ===== inline script 24 ===== */
/**
 * Game Header: clone the existing .landing-header.header-top as a second header
 * and swap visibility based on Game Mode. If the original has any IDs inside,
 * we strip them from the clone to avoid duplicate IDs.
 */
(function () {
  const ORIGINAL_SEL = '.landing-header.header-top';
  const original = document.querySelector(ORIGINAL_SEL);
  if (!original) return;

  // Avoid double-injecting on reloads
  if (document.getElementById('gameHeader')) return;

  // Deep clone
  const clone = original.cloneNode(true);
  clone.id = 'gameHeader';
  clone.classList.add('game-header'); // used by CSS to control visibility

  // Strip any IDs in the clone to avoid duplicates
  clone.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));

  // Insert right after the original header
  original.insertAdjacentElement('afterend', clone);

  // (Optional) If you want a different logo/text in Game Mode by default:
  // const logoInClone = clone.querySelector('.logo-wrapper .logo');
  // if (logoInClone) logoInClone.textContent = 'GAME MODE'; // you can customize later

  // Hook into the existing Game Mode button (already used in your file).
  // If that listener already toggles body.game-mode-active, we don't need
  // to add anything else here. This block is only a safety net:
  const gameModeBtn = document.getElementById('gameModeBtn');
  if (gameModeBtn && !gameModeBtn.dataset._gmWired) {
    gameModeBtn.dataset._gmWired = '1';
    // No-op: the file already toggles game-mode-active elsewhere.
    // If for any reason it didn’t, uncomment the next line:
    // gameModeBtn.addEventListener('click', () => document.body.classList.toggle('game-mode-active'));
  }
})();


/* ===== inline script 25 ===== */
(function () {

  /* ---- MESSAGES ---- */
  const DEFAULT_ROTATE_MESSAGE =
    'Please rotate your device!';

  const TOO_SMALL_MESSAGE =
    'Your device is not supported.<br>Please use a larger phone, tablet, or desktop.';

  /* ---- CHECK DEVICE SIZE & UPDATE POPUP ---- */
  function updateDeviceState() {

    const w = window.innerWidth || document.documentElement.clientWidth;
    const h = window.innerHeight || document.documentElement.clientHeight;
    const turn = document.getElementById('turn');
    if (!turn) return;

    /*
      Non-compatible ONLY when:
      - width  <= 750
      - height >= 450
      - height <= 550
    */
    const tooSmall = (w <= 750 && h >= 450 && h <= 550);

    // Add/remove body class so CSS can hide/show the page
    document.body.classList.toggle('device-too-small', tooSmall);

    // Set the correct popup text
    if (tooSmall) {
      turn.innerHTML = TOO_SMALL_MESSAGE;
    } else {
      turn.innerHTML = DEFAULT_ROTATE_MESSAGE;
    }
  }

  /* ---- LISTEN FOR SIZE CHANGES ---- */
  window.addEventListener('load', updateDeviceState);
  window.addEventListener('resize', updateDeviceState);

})();

/* ===== inline script 26 ===== */
/* =========================================================
 AUTO REFRESH ON SCREEN SIZE CHANGE
 Guarantees clean UI after any resize / orientation change
 ========================================================= */

(function () {
  let lastW = window.innerWidth;
  let lastH = window.innerHeight;
  let locked = false;

  function checkResize() {
    const w = window.innerWidth;
    const h = window.innerHeight;

    // Only refresh when actual dimensions change
    if ((w !== lastW || h !== lastH) && !locked) {
      locked = true;

      // Allow layout + CSS to settle slightly
      setTimeout(() => {
        location.reload();
      }, 200);
    }

    lastW = w;
    lastH = h;
  }

  window.addEventListener("resize", checkResize, { passive: true });
  window.addEventListener("orientationchange", checkResize, { passive: true });
})();

/* ===== inline script 27 ===== */
/* ===== ADD-ON Emoji -> Gesture + Matching GIF =====
   - Keeps your existing click-capture + close behavior
   - Sets the correct character animation by gestureSelector value
   - Plays the matching GIF for the clicked emoji (not a single dummy gif)
   - GIF convention: images/<emoji-base>.gif (same basename as the .svg)
*/
(function () {
  const FALLBACK_VIDEO_ID = 'video_character1_seat1'; // used only if control system is unavailable

  function getTargetVideo() {
    try {
      if (typeof window.__getGestureTargets === 'function') {
        const vids = window.__getGestureTargets() || [];
        if (vids.length) return vids[0];
      }
    } catch (e) { }
    return document.getElementById(FALLBACK_VIDEO_ID);
  }


  const EMOJI_TO_GESTURE = {
    // Disbelief
    'brain-emoji.svg': '9.50',
    'sad-emoji.svg': '9.50',
    'sad-emoji3.svg': '9.50',

    // Happy
    'dolar-emoji.svg': '21.50',
    'smily-emoji2.svg': '21.50',
    'thumbup-emoji.svg': '21.50',  // supported even if you add it later
    'thumbup-emoji.svg': '21.50',   // supported if you add it later

    // Idle
    'sad-emoji2.svg': '30.50',
    'silence-emoji.svg': '30.50',
    'ill-emoji.svg': '30.50',
    'blind-emoji.svg': '30.50'  // supported if you add it later
  };

  // Optional: if any GIF filenames differ from the SVG basename, override here.
  // Example: 'dolar-emoji.svg': 'images/dollar-emoji.gif'
  const EMOJI_TO_GIF_OVERRIDE = {
    // 'dolar-emoji.svg': 'images/dollar-emoji.gif'
  };

  function baseName(p) {
    const s = (p || '').split('?')[0].split('#')[0];
    return s.substring(s.lastIndexOf('/') + 1);
  }

  function gifForEmojiFile(file) {
    if (EMOJI_TO_GIF_OVERRIDE[file]) return EMOJI_TO_GIF_OVERRIDE[file];
    // Default convention: swap .svg -> .gif in same images/ folder
    if (file.endsWith('.svg')) return 'images/' + file.replace(/\.svg$/i, '.gif');
    return null;
  }

  function getTargetOverlay() {
    const v = getTargetVideo();
    if (!v) return null;
    const box = document.getElementById('overlay_' + v.id);
    if (!box) return null;
    if (box.dataset && box.dataset.empty === '1') return null;
    return box;
  }

  function closeEmojiPanel() {
    const panel = document.querySelector('.emoji-main');
    const inner = document.querySelector('.emoji-main-inner');
    if (panel) panel.style.display = 'none';
    if (inner) inner.classList.remove('add-after', 'd-block', 'active', 'open', 'show');
  }

  function setGestureAndPlay(gestureVal) {
    if (!gestureVal) return;
    const sel = document.getElementById('gestureSelector');
    if (sel) {
      sel.value = gestureVal;
      sel.dispatchEvent(new Event('change', { bubbles: true }));
    }
    if (typeof window.triggerGesture === 'function') window.triggerGesture();
  }

  function ensureLayer(box) {
    let layer = box.querySelector('.reaction-layer');
    if (!layer) {
      layer = document.createElement('div');
      layer.className = 'reaction-layer';
      box.appendChild(layer);
    }
    return layer;
  }

  function ensureGif(layer) {
    let img = layer.querySelector('img.react-gif[data-reaction-demo="1"]');
    if (!img) {
      img = document.createElement('img');
      img.className = 'react-gif';
      img.setAttribute('data-seat1-demo', '1');
      img.alt = '';
      img.decoding = 'async';
      img.style.display = 'none';
      layer.appendChild(img);
    }
    return img;
  }

  function hideLegacyReactionImages(box) {
    box.querySelectorAll('.reaction-layer img, img.react-gif, img.emoji, img.emoji-gif, img.reaction, img.reaction-gif')
      .forEach(el => {
        if (el.getAttribute('data-seat1-demo') === '1') return;
        el.style.display = 'none';
      });
  }

  function getOnePlayMs() {
    const raw = getComputedStyle(document.documentElement).getPropertyValue('--react-gif-ms').trim();
    let ms = 1400;
    if (raw) {
      const n = parseFloat(raw);
      if (!Number.isNaN(n)) ms = raw.endsWith('s') ? n * 1000 : n;
    }
    return Math.max(ms, 1400);
  }

  // Start once with a specific GIF src
  function startOnce(gifSrc) {
    const box = getTargetOverlay();
    if (!box) return null;

    hideLegacyReactionImages(box);

    const layer = ensureLayer(box);
    const img = ensureGif(layer);

    img.style.display = 'none';
    img.onload = () => { img.style.display = 'block'; };
    img.onerror = () => { img.style.display = 'none'; };

    // Cache-bust so repeated clicks replay the same gif reliably
    const bust = (gifSrc.indexOf('?') >= 0 ? '&' : '?') + 'r=' + Date.now() + '_' + Math.random().toString(16).slice(2);
    img.src = gifSrc + bust;

    return img;
  }

  // Play twice (same behavior as your current patch), but using the clicked emoji's gif
  function playTwice(gifSrc) {
    if (!gifSrc) return;
    const one = getOnePlayMs();
    const img1 = startOnce(gifSrc);
    if (!img1) return;

    clearTimeout(img1.__t1); clearTimeout(img1.__t2);

    img1.__t1 = setTimeout(() => { startOnce(gifSrc); }, one);
    img1.__t2 = setTimeout(() => {
      const box = getTargetOverlay();
      if (!box) return;
      const img = box.querySelector('img.react-gif[data-reaction-demo="1"]');
      if (img) img.style.display = 'none';
    }, one * 2);
  }

  // Capture-phase handler (keeps your "close X should not trigger" behavior)
  window.addEventListener('click', function (e) {
    // If clicking the close X inside emoji panel, do NOTHING here (let existing close behavior run)
    const closeBtn = e.target && e.target.closest && e.target.closest('a.buy-cross');
    if (closeBtn) return;

    const emojiImg = e.target && e.target.closest && e.target.closest('.emoji-main #profile-tab-pane img');
    if (!emojiImg) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const file = baseName(emojiImg.getAttribute('src') || emojiImg.src || '');
    const gestureVal = EMOJI_TO_GESTURE[file] || null;
    const gifSrc = gifForEmojiFile(file);

    closeEmojiPanel();

    // 1) Play character animation
    if (gestureVal) setGestureAndPlay(gestureVal);

    // 2) Play the matching gif (twice)
    if (gifSrc) playTwice(gifSrc);

  }, true);
})();


/* ===== inline script 28 ===== */
/* ============================================================
   SAFE: Close popups on outside click (does NOT break open buttons)
   ============================================================ */
(function () {
  if (window.__outsideClosePopupsInstalled) return;
  window.__outsideClosePopupsInstalled = true;

  // Any element that OPENS a popup (add to this list if you have more)
  const OPEN_TRIGGERS_SELECTOR = [
    '#buyInBtn',
    '#timeoutBtn',
    '#exitBtn',
    '#proofBtn',
    '.buy-btn',
    '.time-popup',
    '.exit-popup'
  ].join(',');

  function isVisible(el) {
    if (!el) return false;
    const cs = getComputedStyle(el);
    return cs.display !== 'none' && cs.visibility !== 'hidden' && cs.opacity !== '0';
  }

  function closeAllPopups() {
    document.querySelectorAll('.camera-setting-main').forEach(popup => {
      if (!isVisible(popup)) return;

      // Use the popup's own X button if present (keeps existing state logic intact)
      const closeBtn = popup.querySelector('a.buy-cross');
      if (closeBtn) {
        closeBtn.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
      } else {
        // fallback only if no close button exists
        popup.style.display = 'none';
      }
    });
  }

  document.addEventListener('click', function (e) {
    try {
      const t = e.target;

      // 1) If user clicked INSIDE any popup, do nothing
      if (t && t.closest && t.closest('.camera-setting-main')) return;

      // 2) If user clicked a popup OPEN trigger, do nothing (prevents "open then instantly close")
      if (t && t.closest && t.closest(OPEN_TRIGGERS_SELECTOR)) return;

      // 3) If user clicked a close button, let it handle itself (avoid double handling)
      if (t && t.closest && t.closest('a.buy-cross')) return;

      // 4) Otherwise, click anywhere else closes popups
      closeAllPopups();

    } catch (err) {
      // swallow — never crash UI
    }
  }, false);
})();

/* ===== inline script 29 ===== */
/* ============================================================
   Unclip ONLY .live-pip by mirroring its stream outside overflow:hidden
   - Does NOT move/resize original pip
   - Mirror uses exact screen rect of original (pixel-perfect)
   - FIX: mirror z-index must stay BELOW overlays/cards (overlay-box is z=9)
   ============================================================ */
(function () {
  if (window.__pipMirrorInstalled) return;
  window.__pipMirrorInstalled = true;

  // Videos are z=2, overlays are z=9 -> keep mirror between them
  const MIRROR_Z = 8;

  function ensureMirrorFor(pip) {
    if (!pip || pip.tagName !== 'VIDEO') return null;

    let mirrorId = pip.getAttribute('data-pip-mirror-id');
    let mirror = mirrorId ? document.getElementById(mirrorId) : null;

    if (!mirror) {
      mirrorId = 'pip-mirror-' + Math.random().toString(16).slice(2);
      pip.setAttribute('data-pip-mirror-id', mirrorId);

      mirror = document.createElement('video');
      mirror.id = mirrorId;
      mirror.className = 'live-pip-mirror';
      mirror.autoplay = true;
      mirror.muted = true;          // safest for autoplay
      mirror.playsInline = true;

      // Never block clicks
      mirror.style.pointerEvents = 'none';

      // Not clipped, but NOT top-layer
      mirror.style.position = 'fixed';
      mirror.style.zIndex = String(MIRROR_Z);

      document.body.appendChild(mirror);
    } else {
      // If an old mirror existed from a previous version, force-correct its z-index
      mirror.style.zIndex = String(MIRROR_Z);
    }

    // Mirror the stream/source (supports srcObject streams and file src)
    try {
      if (pip.srcObject && mirror.srcObject !== pip.srcObject) {
        mirror.srcObject = pip.srcObject;
      } else if (!pip.srcObject && pip.currentSrc && mirror.src !== pip.currentSrc) {
        mirror.src = pip.currentSrc;
      }
    } catch (e) { }

    return mirror;
  }

  function syncOne(pip) {
    const mirror = ensureMirrorFor(pip);
    if (!mirror) return;

    // Exact pixel rect of original pip
    const r = pip.getBoundingClientRect();

    // If pip is not visible, hide mirror too
    if (r.width <= 0 || r.height <= 0) {
      mirror.style.display = 'none';
      return;
    }

    mirror.style.display = 'block';
    mirror.style.left = r.left + 'px';
    mirror.style.top = r.top + 'px';
    mirror.style.width = r.width + 'px';
    mirror.style.height = r.height + 'px';
    mirror.style.borderRadius = getComputedStyle(pip).borderRadius;
    mirror.style.objectFit = getComputedStyle(pip).objectFit || 'cover';

    // Keep mirror visually identical
    mirror.style.transform = 'none';
    mirror.style.transformOrigin = '0 0';
  }

  function syncAll() {
    document.querySelectorAll('video.live-pip').forEach(syncOne);
  }

  // Run after layout is ready
  function rafSync() { requestAnimationFrame(syncAll); }

  if (document.readyState === 'complete') rafSync();
  else window.addEventListener('load', rafSync);

  // Update on resize/scroll (pixel-perfect positioning)
  window.addEventListener('resize', rafSync, { passive: true });
  window.addEventListener('scroll', rafSync, { passive: true });

  // Light periodic sync for responsive reflows
  setInterval(rafSync, 250);
})();



/* ===== inline script 30 ===== */
document.addEventListener("DOMContentLoaded", () => {
  // Target ONLY the action area (not the header or cards that share id="gameHeader")
  const actionBar = document.querySelector(".fold-main#gameHeader");
  const checkboxHost = document.getElementById("checkboxContainer");

  if (!actionBar || !checkboxHost) return;

  // Optional: smooth fade like your other UI toggles
  actionBar.style.transition = actionBar.style.transition || "opacity 200ms ease";

  // Build the checkbox UI
  const wrap = document.createElement("label");
  wrap.style.alignItems = "center";
  wrap.style.gap = "10px";
  wrap.style.marginTop = "10px";
  wrap.style.cursor = "pointer";

  const cb = document.createElement("input");
  cb.type = "checkbox";
  cb.id = "toggleActionButtons";

  const text = document.createElement("span");
  text.textContent = "Hide action buttons";

  wrap.appendChild(cb);
  wrap.appendChild(text);
  checkboxHost.appendChild(wrap);

  // Restore previous state (optional)
  const saved = localStorage.getItem("hideActionButtons") === "1";
  cb.checked = saved;
  setHidden(saved);

  cb.addEventListener("change", () => {
    setHidden(cb.checked);
    localStorage.setItem("hideActionButtons", cb.checked ? "1" : "0");
  });

  function setHidden(hide) {
    actionBar.classList.toggle("is-hidden", hide);
    actionBar.setAttribute("aria-hidden", hide ? "true" : "false");
  }
});

/* ===== inline script 31 ===== */
/* ===== SHOW CARDS (CLEAN SINGLE SYSTEM) ===== */
(function () {

  const MASTER_ID = 'showCardsToggle';
  const USER_ID = 'userShowCardsToggle';

  const master = document.getElementById(MASTER_ID);
  if (!master) return;

  const foldMain = document.querySelector('.fold-main#gameHeader') || document.querySelector('.fold-main');
  if (!foldMain) return;

  const masterLabel = master.closest('label') || master.parentElement;

  let btn = document.getElementById('showCardsSelectBtn');
  if (!btn) {
    btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'showCardsSelectBtn';
    btn.textContent = 'Cards For ▾';
    btn.style.cssText =
      'margin-left:10px;padding:4px 10px;border-radius:8px;' +
      'border:1px solid rgba(255,255,255,.25);background:rgba(0,0,0,.25);color:#fff;';
    masterLabel.insertAdjacentElement('afterend', btn);
  }

  let panel = document.getElementById('showCardsSelectPanel');
  if (!panel) {
    panel = document.createElement('div');
    panel.id = 'showCardsSelectPanel';
    panel.style.cssText =
      'display:none;margin-top:8px;padding:10px;border-radius:10px;' +
      'border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.35);' +
      'backdrop-filter:blur(10px);max-width:320px;';

    panel.innerHTML = `
      <div style="display:flex;gap:10px;margin-bottom:8px;">
        <button type="button" id="cardsNoneBtn">None</button>
        <button type="button" id="cardsAllBtn">All</button>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
        ${Array.from({ length: 9 }, (_, i) => `
          <label><input type="checkbox" class="showCardsChar" value="${i + 1}"> C${i + 1}</label>
        `).join('')}
      </div>
    `;
    btn.insertAdjacentElement('afterend', panel);
  }

  btn.onclick = e => {
    e.preventDefault();
    e.stopPropagation();
    panel.style.display = panel.style.display === 'block' ? 'none' : 'block';
  };
  panel.onclick = e => e.stopPropagation();
  document.addEventListener('click', e => {
    if (!panel.contains(e.target) && e.target !== btn) panel.style.display = 'none';
  }, true);

  const noneBtn = panel.querySelector('#cardsNoneBtn');
  const allBtn = panel.querySelector('#cardsAllBtn');
  const charCbs = Array.from(panel.querySelectorAll('.showCardsChar'));

  function selectedSet() {
    return new Set(charCbs.filter(c => c.checked).map(c => c.value));
  }
  function setNone() { charCbs.forEach(c => c.checked = false); }
  function setAll() { charCbs.forEach(c => c.checked = true); }

  // In-game checkbox
  let userWrap = document.getElementById('userShowCardsWrap');
  if (!userWrap) {
    userWrap = document.createElement('label');
    userWrap.id = 'userShowCardsWrap';
    userWrap.innerHTML = `<input type="checkbox" id="${USER_ID}"> Show Cards`;
    (foldMain.querySelector('.fold-btn-main ol') || foldMain).appendChild(userWrap);
  }
  const userCb = document.getElementById(USER_ID);

  function playerCardsActive() {
    return document.body.classList.contains('player-cards-active');
  }

  function clearTargets() {
    document.querySelectorAll('.overlay-box.showcards-on:not([data-playercards="1"])')
      .forEach(el => el.classList.remove('showcards-on'));
  }

  function apply() {
    const keepVisible = master.checked || playerCardsActive();

    document.body.classList.toggle('show-player-cards', keepVisible);
    document.body.classList.toggle(
      'user-cards-on',
      (master.checked && userCb?.checked) || playerCardsActive()
    );

    if (!master.checked || !userCb?.checked) {
      clearTargets();
      return;
    }

    const sel = selectedSet();
    if (!sel.size) { clearTargets(); return; }

    document.querySelectorAll('.overlay-box[id^="overlay_video_character"]').forEach(ov => {
      if (ov.dataset?.empty === '1') return;
      const m = ov.id.match(/overlay_video_character(\d+)_seat/i);
      if (!m) return;

      if (sel.has(m[1])) {
        ov.classList.add('showcards-on');
      } else if (!ov.hasAttribute('data-playercards')) {
        ov.classList.remove('showcards-on');
      }
    });
  }

  noneBtn.onclick = e => { e.preventDefault(); setNone(); apply(); };
  allBtn.onclick = e => { e.preventDefault(); setAll(); apply(); };
  charCbs.forEach(c => c.onchange = apply);

  master.onchange = apply;
  userCb.onchange = apply;

  setNone();
  apply();

})();



/* ===== inline script 32 ===== */

/* ======================================================================
   PLAYER CARDS (PRIVATE VIEW) — SINGLE SELECT
   - Independent visibility gate using body.player-cards-active
   - Does NOT conflict with Show Cards
   ====================================================================== */
(function () {

  const controls = document.getElementById('controls') || document.body;
  if (!controls) return;

  const SHOW_MASTER_ID = 'showCardsToggle';
  const showCardsMaster = document.getElementById(SHOW_MASTER_ID);

  function showCardsSystemIsOn() {
    return !!(showCardsMaster && showCardsMaster.checked);
  }

  /* ------------------------------------------------------------------
     Placement: BEFORE Show Cards
     ------------------------------------------------------------------ */
  const showCardsLabel = showCardsMaster
    ? (showCardsMaster.closest('label') || showCardsMaster.parentElement)
    : null;

  /* ---------- Button ---------- */
  let btn = document.getElementById('playerCardsSelectBtn');
  if (!btn) {
    btn = document.createElement('button');
    btn.type = 'button';
    btn.id = 'playerCardsSelectBtn';
    btn.textContent = 'Player Cards ▾';
    btn.style.cssText =
      'margin-top:0;margin-right:6px;padding:4px 10px;border-radius:8px;' +
      'border:1px solid rgba(255,255,255,.25);background:rgba(0,0,0,.25);color:#fff;';
    if (showCardsLabel && showCardsLabel.parentNode) {
      showCardsLabel.parentNode.insertBefore(btn, showCardsLabel);
    } else {
      controls.insertBefore(btn, controls.firstChild);
    }
  }

  /* ---------- Panel ---------- */
  let panel = document.getElementById('playerCardsSelectPanel');
  if (!panel) {
    panel = document.createElement('div');
    panel.id = 'playerCardsSelectPanel';
    panel.style.cssText =
      'display:none;margin-top:8px;padding:10px;border-radius:10px;' +
      'border:1px solid rgba(255,255,255,.18);background:rgba(0,0,0,.35);' +
      'backdrop-filter:blur(10px);max-width:320px;';

    panel.innerHTML = `
      <div style="display:flex;gap:10px;margin-bottom:8px;">
        <button type="button" id="playerCardsNoneBtn"
          style="padding:4px 10px;border-radius:8px;border:1px solid rgba(255,255,255,.25);
                 background:rgba(0,0,0,.25);color:#fff;">None</button>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
        ${Array.from({ length: 9 }, (_, i) => `
          <label style="display:flex;gap:6px;align-items:center;">
            <input type="radio" name="playerCardsChar" class="playerCardsChar" value="${i + 1}">
            C${i + 1}
          </label>
        `).join('')}
      </div>
    `;
    btn.insertAdjacentElement('afterend', panel);
  }

  /* ---------- Dropdown behavior ---------- */
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    e.stopPropagation();
    panel.style.display = (panel.style.display === 'block' ? 'none' : 'block');
  });

  panel.addEventListener('click', e => e.stopPropagation());
  document.addEventListener('click', e => {
    if (!panel.contains(e.target) && e.target !== btn) panel.style.display = 'none';
  }, true);

  const noneBtn = panel.querySelector('#playerCardsNoneBtn');
  const radios = Array.from(panel.querySelectorAll('.playerCardsChar'));

  /* ------------------------------------------------------------------
     Core logic
     ------------------------------------------------------------------ */

  function clearPlayerTargets() {
    document.querySelectorAll('.overlay-box.showcards-on[data-playercards="1"]').forEach(el => {
      el.classList.remove('showcards-on');
      el.removeAttribute('data-playercards');
    });
  }

  function selectedChar() {
    const r = radios.find(r => r.checked);
    return r ? r.value : null;
  }

  function apply() {
    const ch = selectedChar();

    clearPlayerTargets();

    if (!ch) {
      document.body.classList.remove('player-cards-active');
      if (!showCardsSystemIsOn()) {
        document.body.classList.remove('show-player-cards');
        document.body.classList.remove('user-cards-on');
      }
      return;
    }

    // ---- PRIVATE VISIBILITY GATE ----
    document.body.classList.add('player-cards-active');
    document.body.classList.add('show-player-cards');
    document.body.classList.add('user-cards-on');

    document.querySelectorAll('.overlay-box[id^="overlay_video_character"]').forEach(ov => {
      if (ov.dataset?.empty === '1') return;
      const m = ov.id.match(/overlay_video_character(\d+)_seat/i);
      if (!m) return;
      if (m[1] === String(ch)) {
        ov.classList.add('showcards-on');
        ov.setAttribute('data-playercards', '1');
      }
    });
  }

  noneBtn.addEventListener('click', e => {
    e.preventDefault();
    radios.forEach(r => r.checked = false);
    apply();
  });

  radios.forEach(r => r.addEventListener('change', apply));
  apply();

})();

