/**
 * GENESIS Audio — ambiance + SFX procéduraux (Web Audio API).
 * Discret, pas arcade. Mute mémorisé. Démarre après premier geste (autoplay).
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'genesis-audio-muted';
  var ctx = null;
  var master = null;
  var ambientNodes = [];
  var unlocked = false;
  var muted = false;
  var ambientOn = false;

  try {
    muted = localStorage.getItem(STORAGE_KEY) === '1';
  } catch (e) {
    muted = false;
  }

  function ensureCtx() {
    if (ctx) return ctx;
    var AC = window.AudioContext || window.webkitAudioContext;
    if (!AC) return null;
    ctx = new AC();
    master = ctx.createGain();
    master.gain.value = muted ? 0 : 0.55;
    master.connect(ctx.destination);
    return ctx;
  }

  function setMuted(m) {
    muted = !!m;
    try {
      localStorage.setItem(STORAGE_KEY, muted ? '1' : '0');
    } catch (e) {}
    if (master) {
      master.gain.setTargetAtTime(muted ? 0 : 0.55, ctx.currentTime, 0.05);
    }
    syncToggle();
    if (!muted && unlocked) {
      startAmbient();
    } else if (muted) {
      stopAmbient();
    }
  }

  function unlock() {
    if (unlocked) return;
    var c = ensureCtx();
    if (!c) return;
    unlocked = true;
    if (c.state === 'suspended') {
      c.resume().catch(function () {});
    }
    if (!muted) startAmbient();
  }

  function tone(freq, dur, type, gain, when) {
    var c = ensureCtx();
    if (!c || !master || muted) return;
    var t0 = when != null ? when : c.currentTime;
    var osc = c.createOscillator();
    var g = c.createGain();
    osc.type = type || 'sine';
    osc.frequency.setValueAtTime(freq, t0);
    g.gain.setValueAtTime(0.0001, t0);
    g.gain.exponentialRampToValueAtTime(Math.max(0.0001, gain || 0.08), t0 + 0.02);
    g.gain.exponentialRampToValueAtTime(0.0001, t0 + (dur || 0.2));
    osc.connect(g);
    g.connect(master);
    osc.start(t0);
    osc.stop(t0 + (dur || 0.2) + 0.05);
  }

  function noiseBurst(dur, gain, filterFreq) {
    var c = ensureCtx();
    if (!c || !master || muted) return;
    var n = Math.floor(c.sampleRate * (dur || 0.12));
    var buf = c.createBuffer(1, n, c.sampleRate);
    var data = buf.getChannelData(0);
    for (var i = 0; i < n; i++) data[i] = (Math.random() * 2 - 1) * (1 - i / n);
    var src = c.createBufferSource();
    src.buffer = buf;
    var filt = c.createBiquadFilter();
    filt.type = 'lowpass';
    filt.frequency.value = filterFreq || 800;
    var g = c.createGain();
    g.gain.value = gain || 0.06;
    src.connect(filt);
    filt.connect(g);
    g.connect(master);
    src.start();
  }

  /* ——— SFX catalogue (discret, pas arcade) ——— */
  var sfx = {
    soft: function () {
      tone(520, 0.08, 'sine', 0.04);
      tone(780, 0.1, 'sine', 0.025, (ctx && ctx.currentTime) + 0.04);
    },
    nav: function () {
      noiseBurst(0.1, 0.035, 600);
      tone(220, 0.18, 'triangle', 0.03);
    },
    ok: function () {
      tone(392, 0.12, 'sine', 0.06);
      tone(523, 0.16, 'sine', 0.05, (ctx && ctx.currentTime) + 0.08);
    },
    warn: function () {
      tone(180, 0.22, 'sawtooth', 0.035);
      tone(140, 0.28, 'triangle', 0.04, (ctx && ctx.currentTime) + 0.1);
    },
    loss: function () {
      tone(160, 0.35, 'triangle', 0.05);
      tone(110, 0.45, 'sine', 0.04, (ctx && ctx.currentTime) + 0.12);
      noiseBurst(0.2, 0.04, 400);
    },
    discovery: function () {
      tone(440, 0.1, 'sine', 0.05);
      tone(554, 0.12, 'sine', 0.045, (ctx && ctx.currentTime) + 0.07);
      tone(659, 0.18, 'sine', 0.04, (ctx && ctx.currentTime) + 0.14);
    },
    task: function () {
      tone(330, 0.1, 'sine', 0.04);
      tone(495, 0.14, 'triangle', 0.03, (ctx && ctx.currentTime) + 0.06);
    },
    recon: function () {
      noiseBurst(0.14, 0.03, 1200);
      tone(280, 0.15, 'sine', 0.035);
      tone(420, 0.12, 'triangle', 0.025, (ctx && ctx.currentTime) + 0.08);
    },
    capture: function () {
      tone(200, 0.1, 'triangle', 0.045);
      tone(300, 0.12, 'sine', 0.04, (ctx && ctx.currentTime) + 0.06);
      noiseBurst(0.08, 0.03, 500);
    },
    mission: function () {
      tone(165, 0.2, 'sine', 0.05);
      tone(247, 0.22, 'triangle', 0.04, (ctx && ctx.currentTime) + 0.1);
      tone(330, 0.18, 'sine', 0.03, (ctx && ctx.currentTime) + 0.2);
    },
    mutate: function () {
      tone(180, 0.15, 'sawtooth', 0.025);
      tone(240, 0.2, 'sine', 0.04, (ctx && ctx.currentTime) + 0.05);
      tone(360, 0.18, 'triangle', 0.03, (ctx && ctx.currentTime) + 0.14);
    },
    cross: function () {
      tone(294, 0.12, 'sine', 0.04);
      tone(370, 0.14, 'sine', 0.04, (ctx && ctx.currentTime) + 0.08);
      tone(440, 0.16, 'triangle', 0.035, (ctx && ctx.currentTime) + 0.16);
    },
    craft: function () {
      tone(220, 0.08, 'square', 0.02);
      tone(330, 0.1, 'triangle', 0.035, (ctx && ctx.currentTime) + 0.05);
      tone(440, 0.12, 'sine', 0.03, (ctx && ctx.currentTime) + 0.12);
    },
    heal: function () {
      tone(392, 0.14, 'sine', 0.045);
      tone(523, 0.18, 'sine', 0.04, (ctx && ctx.currentTime) + 0.1);
    },
    seuil: function () {
      tone(196, 0.2, 'sine', 0.05);
      tone(294, 0.22, 'sine', 0.045, (ctx && ctx.currentTime) + 0.12);
      tone(392, 0.28, 'triangle', 0.04, (ctx && ctx.currentTime) + 0.24);
    },
  };

  /** Choisit un SFX selon action formulaire ou flash. */
  function sfxForAction(act) {
    act = (act || '').toLowerCase();
    if (act === 'set_team') return 'soft';
    if (act === 'recon' || act === 'select_zone') return 'recon';
    if (act === 'analyze') return 'capture';
    if (act === 'mission') return 'mission';
    if (act === 'mutate') return 'mutate';
    if (act === 'cross') return 'cross';
    if (act === 'craft_drone') return 'craft';
    if (act === 'recover') return 'heal';
    if (act === 'study') return 'soft';
    if (act === 'open_baie' || act === 'depart') return 'seuil';
    if (act === 'exhibit') return 'discovery';
    if (act === 'farm' || act === 'restock') return 'ok';
    return 'nav';
  }

  function sfxForFlash(el) {
    if (!el) return 'discovery';
    if (el.classList.contains('flash-banner--loss')) return 'loss';
    if (el.classList.contains('flash-banner--horizon')) return 'discovery';
    if (el.classList.contains('flash-banner--contact')) return 'ok';
    var title = ((el.querySelector('strong') || {}).textContent || '').toLowerCase();
    var body = ((el.querySelector('span') || {}).textContent || '').toLowerCase();
    var t = title + ' ' + body;
    if (t.indexOf('mort') !== -1 || t.indexOf('perte') !== -1 || t.indexOf('bris') !== -1 || t.indexOf('fatale') !== -1) return 'loss';
    if (t.indexOf('mission') !== -1) return 'mission';
    if (t.indexOf('croisement') !== -1 || t.indexOf('union') !== -1) return 'cross';
    if (t.indexOf('mutation') !== -1) return 'mutate';
    if (t.indexOf('capture') !== -1 || t.indexOf('prise') !== -1) return 'capture';
    if (t.indexOf('assembl') !== -1 || t.indexOf('filet') !== -1 || t.indexOf('sonde') !== -1) return 'craft';
    if (t.indexOf('soin') !== -1 || t.indexOf('soign') !== -1) return 'heal';
    if (t.indexOf('spatio') !== -1 || t.indexOf('seuil') !== -1 || t.indexOf('baie') !== -1) return 'seuil';
    if (t.indexOf('fil libre') !== -1) return 'ok';
    if (t.indexOf('recon') !== -1 || t.indexOf('zone') !== -1 || t.indexOf('contact') !== -1) return 'recon';
    return 'discovery';
  }

  function play(name) {
    if (muted) return;
    unlock();
    var fn = sfx[name];
    if (fn) {
      try {
        fn();
      } catch (e) {}
    }
  }

  function startAmbient() {
    var c = ensureCtx();
    if (!c || !master || muted || ambientOn) return;
    ambientOn = true;
    var t0 = c.currentTime;

    function drone(freq, gainVal, type) {
      var osc = c.createOscillator();
      var g = c.createGain();
      var lfo = c.createOscillator();
      var lfoG = c.createGain();
      osc.type = type || 'sine';
      osc.frequency.value = freq;
      lfo.frequency.value = 0.07 + Math.random() * 0.05;
      lfoG.gain.value = freq * 0.004;
      lfo.connect(lfoG);
      lfoG.connect(osc.frequency);
      g.gain.setValueAtTime(0.0001, t0);
      g.gain.exponentialRampToValueAtTime(gainVal, t0 + 2.5);
      osc.connect(g);
      g.connect(master);
      osc.start(t0);
      lfo.start(t0);
      ambientNodes.push(osc, lfo, g);
    }

    // Canopée basse + rivière teal
    drone(55, 0.018, 'sine');
    drone(82.5, 0.012, 'triangle');
    drone(110, 0.008, 'sine');

    // Léger "vent" filtré intermittent
    var noiseLen = c.sampleRate * 4;
    var nbuf = c.createBuffer(1, noiseLen, c.sampleRate);
    var nd = nbuf.getChannelData(0);
    for (var i = 0; i < noiseLen; i++) nd[i] = Math.random() * 2 - 1;
    var nsrc = c.createBufferSource();
    nsrc.buffer = nbuf;
    nsrc.loop = true;
    var nf = c.createBiquadFilter();
    nf.type = 'bandpass';
    nf.frequency.value = 420;
    nf.Q.value = 0.6;
    var ng = c.createGain();
    ng.gain.value = 0.012;
    nsrc.connect(nf);
    nf.connect(ng);
    ng.connect(master);
    nsrc.start(t0);
    ambientNodes.push(nsrc, nf, ng);
  }

  function stopAmbient() {
    ambientNodes.forEach(function (n) {
      try {
        if (n.stop) n.stop();
        if (n.disconnect) n.disconnect();
      } catch (e) {}
    });
    ambientNodes = [];
    ambientOn = false;
  }

  function syncToggle() {
    var btn = document.getElementById('audio-toggle');
    if (!btn) return;
    btn.setAttribute('aria-pressed', muted ? 'true' : 'false');
    btn.classList.toggle('is-muted', muted);
    btn.title = muted ? 'Activer le son' : 'Couper le son';
    var label = btn.querySelector('.audio-toggle__label');
    if (label) label.textContent = muted ? 'Son off' : 'Son';
  }

  function bindToggle() {
    var btn = document.getElementById('audio-toggle');
    if (!btn || btn.getAttribute('data-bound') === '1') return;
    btn.setAttribute('data-bound', '1');
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      unlock();
      setMuted(!muted);
      if (!muted) play('soft');
    });
    syncToggle();
  }

  // Premier geste utilisateur
  function onFirstGesture() {
    unlock();
    document.removeEventListener('pointerdown', onFirstGesture, true);
    document.removeEventListener('keydown', onFirstGesture, true);
  }
  document.addEventListener('pointerdown', onFirstGesture, true);
  document.addEventListener('keydown', onFirstGesture, true);

  function onPageReady() {
    bindToggle();
    // Flash → SFX contextuel
    var flash = document.querySelector('.flash-banner');
    if (flash && flash.getAttribute('data-audio-played') !== '1') {
      flash.setAttribute('data-audio-played', '1');
      play(sfxForFlash(flash));
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onPageReady);
  } else {
    onPageReady();
  }

  // Re-bind après soft-swap
  document.addEventListener('genesis:page', onPageReady);

  window.GenesisAudio = {
    play: play,
    setMuted: setMuted,
    isMuted: function () {
      return muted;
    },
    unlock: unlock,
    bindToggle: bindToggle,
    sfxForAction: sfxForAction,
    sfxForFlash: sfxForFlash,
  };
})();
