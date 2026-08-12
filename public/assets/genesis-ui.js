/**
 * GENESIS UI — fluidité sans rechargement navigateur.
 * - set_team → POST api.php (patch HUD / force-picks)
 * - navigation & actions lourdes → soft-swap HTML (fetch + replace main)
 * - fallback : navigation classique si fetch impossible
 */
(function () {
  'use strict';

  var API = 'api.php';
  var softBusy = false;
  var pageBusy = false;
  var taskRaf = 0;
  var fetchCtrl = null;

  function $(sel, root) {
    return (root || document).querySelector(sel);
  }
  function $$(sel, root) {
    return Array.prototype.slice.call((root || document).querySelectorAll(sel));
  }
  function wait(ms) {
    return new Promise(function (resolve) {
      setTimeout(resolve, ms);
    });
  }

  function audioPlay(name) {
    try {
      if (window.GenesisAudio && typeof window.GenesisAudio.play === 'function') {
        window.GenesisAudio.play(name);
      }
    } catch (e) {}
  }

  function emitPageEvent() {
    try {
      document.dispatchEvent(new CustomEvent('genesis:page'));
    } catch (e) {}
  }

  /* ——— Toasts ——— */
  function toast(msg, kind) {
    if (!msg) return;
    var host = $('#genesis-toast-host');
    if (!host) {
      host = document.createElement('div');
      host.id = 'genesis-toast-host';
      host.setAttribute('aria-live', 'polite');
      document.body.appendChild(host);
    }
    var el = document.createElement('div');
    el.className = 'genesis-toast' + (kind ? ' genesis-toast--' + kind : '');
    el.textContent = msg;
    host.appendChild(el);
    requestAnimationFrame(function () {
      el.classList.add('is-in');
    });
    audioPlay(kind === 'warn' ? 'warn' : 'ok');
    setTimeout(function () {
      el.classList.remove('is-in');
      el.classList.add('is-out');
      setTimeout(function () {
        if (el.parentNode) el.parentNode.removeChild(el);
      }, 400);
    }, 3000);
  }

  function textStrong(chip, value) {
    if (!chip) return;
    var s = chip.querySelector('strong');
    if (s) s.textContent = String(value);
  }

  /* ——— Soft API : HUD + expedition ——— */
  function applyHud(data) {
    if (!data) return;
    var r = data.resources || {};
    $$('.hud-chip--res').forEach(function (chip) {
      var key = '';
      chip.className.split(/\s+/).forEach(function (c) {
        if (c.indexOf('hud-chip--res-') === 0) key = c.slice('hud-chip--res-'.length);
      });
      if (key && r[key] != null) textStrong(chip, r[key]);
    });

    var h = data.hud || {};
    var map = {
      'hud-sondes': function (el) {
        el.textContent = 'Sondes ' + (h.recon_ready || 0) + '/' + (h.recon_total || 0);
      },
      'hud-filets': function (el) {
        el.textContent = 'Filets ' + (h.capture_ready || 0) + '/' + (h.capture_total || 0);
      },
      'hud-lentilles': function (el) {
        el.textContent = 'Lentilles ' + (h.study_ready || 0) + '/' + (h.study_total || 0);
      },
      'hud-formes': function (el) {
        el.textContent = 'Formes ' + (h.species || 0);
      },
    };
    Object.keys(map).forEach(function (id) {
      var el = document.getElementById(id);
      if (el) map[id](el);
    });
  }

  function applyExpedition(data) {
    if (!data || !data.expedition) return;
    var ex = data.expedition;

    $$('button[name="capture_count"]').forEach(function (btn) {
      var n = parseInt(btn.value, 10);
      btn.classList.toggle('is-on', n === ex.capture_count);
    });
    $$('button[name="study_count"]').forEach(function (btn) {
      var n = parseInt(btn.value, 10);
      btn.classList.toggle('is-on', n === ex.study_count);
    });
    $$('button[name="drone_count"]').forEach(function (btn) {
      var n = parseInt(btn.value, 10);
      btn.classList.toggle('is-on', n === ex.drone_count);
    });
    var hiddenRecon = document.getElementById('recon-drone-count');
    if (hiddenRecon) hiddenRecon.value = String(ex.drone_count);

    (data.capture_chance_table || []).forEach(function (row) {
      var btn = $('button[name="capture_count"][value="' + row.n + '"]');
      if (!btn) return;
      var pct = btn.querySelector('.force-pick__pct');
      if (pct && row.chance != null) pct.textContent = '~' + row.chance + '%';
      if (row.tooltip) btn.setAttribute('data-tip', row.tooltip);
      if (row.available === false) btn.disabled = true;
      else if (row.available === true) btn.disabled = false;
    });

    (data.study_chance_table || []).forEach(function (row) {
      var btn = $('button[name="study_count"][value="' + row.n + '"]');
      if (!btn) return;
      if (row.tooltip) btn.setAttribute('data-tip', row.tooltip);
      if (row.available === false && row.n > 0) btn.disabled = true;
      else btn.disabled = false;
    });

    (data.recon_chance_table || []).forEach(function (row) {
      var btn = $('button[name="drone_count"][value="' + row.n + '"]');
      if (!btn) return;
      var pct = btn.querySelector('.force-pick__pct');
      if (pct && row.chance != null) pct.textContent = '~' + row.chance + '%';
      if (row.tooltip) btn.setAttribute('data-tip', row.tooltip);
    });

    var escorts = ex.capture_escorts || [];
    $$('.capture-support__btn').forEach(function (btn) {
      var form = btn.closest('form');
      var input = form && form.querySelector('[name="capture_escort_toggle"]');
      var id = input ? input.value : '';
      var on = escorts.indexOf(id) !== -1;
      btn.classList.toggle('is-on', on);
      var mod = btn.querySelector('.capture-support__mod');
      if (mod && mod.textContent.indexOf('—') === -1) {
        var raw = mod.textContent.replace(/^\+/, '');
        if (!btn.classList.contains('is-hurt')) {
          mod.textContent = (on ? '' : '+') + raw.replace(/^\+/, '');
        }
      }
    });

    var capChance = data.capture_chance;
    $$('[data-live="capture-chance"]').forEach(function (el) {
      if (capChance != null) el.textContent = '~' + capChance + '%';
    });
    $$('[data-live="capture-count"]').forEach(function (el) {
      el.textContent = '×' + ex.capture_count;
    });
    $$('[data-live="capture-escorts-n"]').forEach(function (el) {
      var max = el.getAttribute('data-max') || '3';
      el.textContent = escorts.length + '/' + max;
    });
    $$('[data-live="capture-cta"]').forEach(function (el) {
      var parts = 'Lancer la prise';
      if (capChance != null) parts += ' (~' + capChance + '%';
      parts += ' · ×' + ex.capture_count;
      if (escorts.length) parts += ' · ' + escorts.length + ' appui' + (escorts.length > 1 ? 's' : '');
      if (capChance != null) parts += ')';
      el.textContent = parts;
    });

    $$('[data-live="mutate-chance"]').forEach(function (el) {
      if (data.mutate_chance != null) el.textContent = '~' + data.mutate_chance + '%';
    });
    $$('[data-live="cross-chance"]').forEach(function (el) {
      if (data.cross_chance != null) el.textContent = '~' + data.cross_chance + '%';
    });
    $$('[data-live="study-count"]').forEach(function (el) {
      el.textContent = '×' + ex.study_count;
    });
    $$('[data-live="study-ready"]').forEach(function (el) {
      var h = data.hud || {};
      if (h.study_ready != null) el.textContent = h.study_ready + ' prêtes';
    });
  }

  function applySoft(data) {
    applyHud(data);
    applyExpedition(data);
    if (data.toast) toast(data.toast, 'ok');
    else audioPlay('soft');
  }

  function isSoftApiForm(form) {
    if (!form || form.method.toLowerCase() !== 'post') return false;
    if (form.getAttribute('data-soft') === '0') return false;
    if (form.getAttribute('data-soft') === '1') return true;
    var action = form.querySelector('[name="action"]');
    var act = action ? action.value : '';
    return act === 'set_team';
  }

  function formToBody(form, submitter) {
    var fd = new FormData(form);
    if (submitter && submitter.name) {
      fd.set(submitter.name, submitter.value);
    }
    var screen = document.body.getAttribute('data-screen') || '';
    if (screen && !fd.has('ui_screen')) fd.set('ui_screen', screen);
    return fd;
  }

  function softApiSubmit(form, submitter) {
    if (softBusy) return Promise.resolve();
    softBusy = true;
    document.body.classList.add('is-soft-busy');
    var fd = formToBody(form, submitter);
    return fetch(API, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'GenesisSoft' },
      credentials: 'same-origin',
    })
      .then(function (r) {
        return r.json().then(function (j) {
          return { ok: r.ok, json: j };
        });
      })
      .then(function (res) {
        if (!res.json || !res.json.ok) {
          if (res.json && res.json.reload) {
            return softPageFromForm(form, submitter);
          }
          toast((res.json && res.json.message) || 'Échec du réglage', 'warn');
          return;
        }
        applySoft(res.json);
        document.body.classList.add('is-soft-pulse');
        setTimeout(function () {
          document.body.classList.remove('is-soft-pulse');
        }, 280);
      })
      .catch(function () {
        return softPageFromForm(form, submitter);
      })
      .finally(function () {
        softBusy = false;
        document.body.classList.remove('is-soft-busy');
      });
  }

  /* ——— Soft page swap ——— */
  function extractMain(html) {
    var doc = new DOMParser().parseFromString(html, 'text/html');
    var main = doc.querySelector('main.shell');
    if (!main) return null;
    return {
      main: main,
      bodyClass: doc.body.className || '',
      screen: doc.body.getAttribute('data-screen') || '',
      title: doc.title || document.title,
    };
  }

  function cancelTaskLoop() {
    if (taskRaf) {
      cancelAnimationFrame(taskRaf);
      taskRaf = 0;
    }
  }

  function abortFetch() {
    if (fetchCtrl) {
      try {
        fetchCtrl.abort();
      } catch (e) {}
      fetchCtrl = null;
    }
  }

  function applyEnterAnimations() {
    $$('.grid > .panel, .hero').forEach(function (el, i) {
      el.style.setProperty('--enter-delay', Math.min(i * 35, 280) + 'ms');
      el.classList.remove('js-enter');
      void el.offsetWidth;
      el.classList.add('js-enter');
    });
  }

  function dismissEl(el) {
    if (!el || el.classList.contains('is-leaving')) return;
    el.classList.add('is-leaving');
    setTimeout(function () {
      if (el.parentNode) el.parentNode.removeChild(el);
    }, 380);
  }

  function initEphemeral() {
    $$('[data-dismiss-ms]').forEach(function (el) {
      if (el.getAttribute('data-ephemeral-bound') === '1') return;
      el.setAttribute('data-ephemeral-bound', '1');
      var ms = parseInt(el.getAttribute('data-dismiss-ms') || '3200', 10);
      if (isNaN(ms) || ms < 800) ms = 3200;
      var closeBtn = el.querySelector('.flash-banner__close');
      if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();
          dismissEl(el);
        });
      }
      if (el.classList.contains('flash-banner--horizon')) {
        ms = Math.max(ms, 8000);
      }
      setTimeout(function () {
        dismissEl(el);
      }, ms);
    });
  }

  function initTaskDock() {
    cancelTaskLoop();
    var dock = document.getElementById('task-dock');
    if (!dock) return;
    var serverNow = parseInt(dock.getAttribute('data-server-now') || '0', 10);
    var clientStart = Date.now() / 1000;
    var reloaded = false;

    function tick() {
      var skew = Date.now() / 1000 - clientStart;
      var now = serverNow + skew;
      var allDone = true;
      dock.querySelectorAll('.task-chip').forEach(function (chip) {
        var started = parseInt(chip.getAttribute('data-started') || '0', 10);
        var duration = Math.max(0, parseInt(chip.getAttribute('data-duration') || '1', 10));
        var elapsed = Math.max(0, now - started);
        var pct = duration <= 0 ? 100 : Math.min(100, Math.floor((elapsed / duration) * 100));
        var rem = duration <= 0 ? 0 : Math.max(0, Math.ceil(duration - elapsed));
        var donut = chip.querySelector('.donut');
        if (donut) donut.style.setProperty('--p', String(pct));
        var eta = chip.querySelector('.task-chip__eta');
        if (eta) eta.textContent = rem > 0 ? rem + 's' : '…';
        if (rem > 0) allDone = false;
      });
      if (allDone) {
        if (!reloaded) {
          reloaded = true;
          audioPlay('task');
          softNavigate(window.location.href, { replace: true, reason: 'tasks-done' });
        }
        return;
      }
      taskRaf = requestAnimationFrame(tick);
    }
    taskRaf = requestAnimationFrame(tick);
  }

  function bindPage() {
    document.body.classList.add('is-page-ready');
    document.body.classList.remove('is-page-leave', 'is-page-busy');
    applyEnterAnimations();
    initEphemeral();
    initTaskDock();
    emitPageEvent();
  }

  function normalizeHistoryUrl(url) {
    try {
      var u = new URL(url, window.location.href);
      if (u.origin !== window.location.origin) return url;
      // Garder path + query relatifs à l’app
      return u.pathname + u.search + u.hash;
    } catch (e) {
      return url;
    }
  }

  function applyParsed(parsed, url, opts) {
    opts = opts || {};
    var current = $('main.shell');
    if (!current || !parsed.main) {
      window.location.href = url;
      return;
    }
    cancelTaskLoop();

    // Conserver le host de toasts hors main
    var toastHost = $('#genesis-toast-host');
    if (toastHost && toastHost.parentNode === current) {
      document.body.appendChild(toastHost);
    }

    current.replaceWith(parsed.main);

    // Préserver classes runtime soft sur le body
    var runtime = [];
    ['is-page-ready', 'is-soft-busy', 'is-soft-pulse'].forEach(function (c) {
      if (document.body.classList.contains(c)) runtime.push(c);
    });
    document.body.className = parsed.bodyClass;
    runtime.forEach(function (c) {
      document.body.classList.add(c);
    });
    document.body.classList.remove('is-page-leave', 'is-page-busy');

    if (parsed.screen) {
      document.body.setAttribute('data-screen', parsed.screen);
    }
    document.title = parsed.title;

    var histUrl = normalizeHistoryUrl(url);
    if (!opts.skipHistory) {
      if (opts.replace) {
        history.replaceState({ soft: 1 }, '', histUrl);
      } else {
        history.pushState({ soft: 1 }, '', histUrl);
      }
    }

    if (!opts.keepScroll) {
      window.scrollTo({ top: 0, behavior: 'auto' });
    }
    bindPage();
  }

  function softNavigate(url, opts) {
    opts = opts || {};
    if (pageBusy && opts.reason !== 'tasks-done') return Promise.resolve();
    abortFetch();
    pageBusy = true;
    document.body.classList.add('is-page-busy', 'is-page-leave');
    audioPlay('nav');
    fetchCtrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    return fetch(url, {
      method: 'GET',
      credentials: 'same-origin',
      signal: fetchCtrl ? fetchCtrl.signal : undefined,
      headers: {
        'X-Requested-With': 'GenesisSoft',
        Accept: 'text/html',
      },
    })
      .then(function (r) {
        if (!r.ok) throw new Error('nav ' + r.status);
        return r.text().then(function (html) {
          return { html: html, finalUrl: r.url || url };
        });
      })
      .then(function (res) {
        var parsed = extractMain(res.html);
        if (!parsed) throw new Error('no-main');
        return wait(120).then(function () {
          applyParsed(parsed, res.finalUrl || url, opts);
        });
      })
      .catch(function (err) {
        if (err && err.name === 'AbortError') return;
        window.location.href = url;
      })
      .finally(function () {
        pageBusy = false;
        fetchCtrl = null;
        document.body.classList.remove('is-page-busy');
      });
  }

  function actionFromForm(form, submitter) {
    if (submitter && submitter.name === 'action') return String(submitter.value || '');
    var input = form.querySelector('[name="action"]');
    return input ? String(input.value || '') : '';
  }

  function audioForAction(act) {
    try {
      if (window.GenesisAudio && typeof window.GenesisAudio.sfxForAction === 'function') {
        audioPlay(window.GenesisAudio.sfxForAction(act));
        return;
      }
    } catch (e) {}
    audioPlay('nav');
  }

  function softPageFromForm(form, submitter) {
    if (pageBusy) return Promise.resolve();
    abortFetch();
    pageBusy = true;
    document.body.classList.add('is-page-busy', 'is-page-leave');
    audioForAction(actionFromForm(form, submitter));
    var fd = formToBody(form, submitter);
    var actionUrl = form.getAttribute('action') || window.location.href;
    fetchCtrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    return fetch(actionUrl, {
      method: 'POST',
      body: fd,
      credentials: 'same-origin',
      signal: fetchCtrl ? fetchCtrl.signal : undefined,
      headers: {
        'X-Requested-With': 'GenesisSoft',
        Accept: 'text/html',
      },
      redirect: 'follow',
    })
      .then(function (r) {
        if (!r.ok) throw new Error('post ' + r.status);
        return r.text().then(function (html) {
          return { html: html, finalUrl: r.url || actionUrl };
        });
      })
      .then(function (res) {
        var parsed = extractMain(res.html);
        if (!parsed) throw new Error('no-main');
        return wait(120).then(function () {
          applyParsed(parsed, res.finalUrl, { replace: false });
        });
      })
      .catch(function (err) {
        if (err && err.name === 'AbortError') return;
        form.removeEventListener('submit', onFormSubmit, true);
        form.submit();
      })
      .finally(function () {
        pageBusy = false;
        fetchCtrl = null;
        document.body.classList.remove('is-page-busy');
      });
  }

  function formConfirmed(form) {
    var msg = form.getAttribute('data-confirm');
    if (!msg) {
      var attr = form.getAttribute('onsubmit') || '';
      var m = attr.match(/confirm\s*\(\s*['"]([^'"]*)['"]\s*\)/);
      if (!m) return true;
      msg = m[1];
    }
    return window.confirm(msg);
  }

  function onFormSubmit(e) {
    var form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if (form.getAttribute('data-no-soft') === '1') return;
    if (form.getAttribute('target') === '_blank') return;
    if (!formConfirmed(form)) {
      e.preventDefault();
      return;
    }
    e.preventDefault();
    e.stopPropagation();
    var submitter = e.submitter || document.activeElement;
    if (isSoftApiForm(form)) {
      softApiSubmit(form, submitter);
    } else {
      softPageFromForm(form, submitter);
    }
  }

  /** Liens soft : uniquement navigation in-app GENESIS. */
  function isSoftNavLink(a) {
    if (!a || a.target === '_blank' || a.hasAttribute('download')) return false;
    var href = a.getAttribute('href');
    if (!href || href.charAt(0) === '#') return false;
    if (href.indexOf('javascript:') === 0) return false;
    if (href.indexOf('mailto:') === 0 || href.indexOf('tel:') === 0) return false;
    if (href.indexOf('reset.php') !== -1) return false;
    if (href.indexOf('assets/') !== -1) return false;

    // Query-only ou index.php
    if (href.charAt(0) === '?') return true;
    if (href.indexOf('index.php') === 0) return true;

    try {
      var u = new URL(a.href, window.location.href);
      if (u.origin !== window.location.origin) return false;
      // Même dossier public
      var here = window.location.pathname.replace(/\/[^/]*$/, '/');
      var there = u.pathname;
      if (there.indexOf(here) === 0 || there.endsWith('/index.php') || there.endsWith('/public/') || there.endsWith('/public')) {
        // Pas de fichiers statiques
        if (/\.(css|js|png|jpe?g|gif|svg|webp|mp3|wav|json)$/i.test(there)) return false;
        return true;
      }
    } catch (e) {
      return false;
    }
    return false;
  }

  function onNavClick(e) {
    if (e.defaultPrevented) return;
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
    var a = e.target.closest && e.target.closest('a[href]');
    if (!a || !isSoftNavLink(a)) return;
    e.preventDefault();
    softNavigate(a.href);
  }

  document.addEventListener('submit', onFormSubmit, true);
  document.addEventListener('click', onNavClick, false);

  window.addEventListener('popstate', function () {
    softNavigate(window.location.href, { skipHistory: true, replace: true });
  });

  // State initial pour back-button
  if (!history.state || !history.state.soft) {
    try {
      history.replaceState({ soft: 1 }, '', window.location.href);
    } catch (e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindPage);
  } else {
    bindPage();
  }

  window.GenesisUI = {
    softNavigate: softNavigate,
    toast: toast,
  };
})();
