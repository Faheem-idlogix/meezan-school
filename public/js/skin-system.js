/**
 * Meezan School — Skin System (Vanilla JS + jQuery)
 * Handles live switching, localStorage persistence, and optional server-save via AJAX.
 */
(function () {
  'use strict';

  var STORAGE_KEY = 'meezan_skin';
  var DEFAULT_SKIN = 'cyber';
  var htmlEl = document.documentElement;

  /* ── Helpers ── */
  function getCurrentSkin() {
    return htmlEl.getAttribute('data-skin') || localStorage.getItem(STORAGE_KEY) || DEFAULT_SKIN;
  }

  function applySkin(skinId) {
    htmlEl.setAttribute('data-skin', skinId);
    localStorage.setItem(STORAGE_KEY, skinId);
    highlightSelected(skinId);
  }

  function highlightSelected(skinId) {
    var cards = document.querySelectorAll('.skin-card');
    cards.forEach(function (card) {
      var id = card.getAttribute('data-skin-id');
      var check = card.querySelector('.selected-check');
      if (id === skinId) {
        card.classList.add('selected');
        if (check) check.style.display = 'flex';
      } else {
        card.classList.remove('selected');
        if (check) check.style.display = 'none';
      }
    });

    // Update apply button state
    var applyBtn = document.getElementById('skinApplyBtn');
    if (applyBtn) {
      applyBtn.disabled = false;
    }
  }

  /* ── Save to server (if user is logged in) ── */
  function saveToServer(skinId, btn) {
    var token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
      token = document.querySelector('input[name="_token"]');
    }
    var csrfValue = token ? (token.getAttribute('content') || token.value) : '';

    // Update button state
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split saved-icon"></i> Saving…';
    }

    $.ajax({
      url: '/user/skin',
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': csrfValue, 'Accept': 'application/json' },
      data: { skin: skinId },
      success: function () {
        if (btn) {
          btn.innerHTML = '<i class="bi bi-check-circle saved-icon"></i> Saved!';
          setTimeout(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-palette saved-icon"></i> Apply Skin';
          }, 1800);
        }
        if (typeof toastr !== 'undefined') {
          toastr.success('Skin applied successfully!');
        }
      },
      error: function () {
        // Still applied locally even if server save fails
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="bi bi-palette saved-icon"></i> Apply Skin';
        }
        if (typeof toastr !== 'undefined') {
          toastr.warning('Skin applied locally. Server save failed.');
        }
      }
    });
  }

  /* ── Boot: apply skin immediately ── */
  var savedSkin = localStorage.getItem(STORAGE_KEY);
  if (savedSkin) {
    htmlEl.setAttribute('data-skin', savedSkin);
  }

  /* ── DOM Ready: bind events ── */
  $(function () {
    var activeSkin = getCurrentSkin();
    highlightSelected(activeSkin);

    // Skin card click → live preview
    $(document).on('click', '.skin-card', function () {
      var skinId = $(this).data('skin-id');
      if (skinId) {
        applySkin(skinId);
      }
    });

    // Apply button → save to server
    $(document).on('click', '#skinApplyBtn', function () {
      var skinId = getCurrentSkin();
      saveToServer(skinId, this);
    });

    // Reset button → revert to default
    $(document).on('click', '#skinResetBtn', function () {
      applySkin(DEFAULT_SKIN);
      saveToServer(DEFAULT_SKIN, document.getElementById('skinApplyBtn'));
    });

    // Toggle skeleton/bars preview
    $(document).on('click', '#togglePreviewBtn', function () {
      var previews = document.querySelectorAll('.skin-preview');
      previews.forEach(function (p) {
        var bars = p.querySelector('.preview-bars');
        var skel = p.querySelector('.preview-skeleton');
        if (bars && skel) {
          if (bars.style.display === 'none') {
            bars.style.display = 'flex';
            skel.style.display = 'none';
          } else {
            bars.style.display = 'none';
            skel.style.display = 'block';
          }
        }
      });
      var icon = this.querySelector('i');
      if (icon) {
        icon.classList.toggle('bi-bar-chart-fill');
        icon.classList.toggle('bi-grid-3x3-gap-fill');
      }
    });
  });

})();
