{{-- Cookie Consent Modal (Bootstrap 5) --}}
@php
  // su privacy-policy NON deve essere bloccante e NON deve auto-aprirsi
  $disableConsentModalHere = request()->routeIs('privacy.policy');
@endphp

@if(
  !$disableConsentModalHere &&
  (($site?->cookie_banner_enabled ?? false) && !empty($site?->cookie_banner_html))
)
  <div class="modal fade" id="cookieConsentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">
            <i class="fa-solid fa-cookie-bite me-2"></i> Cookie
          </h5>
        </div>

        <div class="modal-body">
          {!! $site->cookie_banner_html !!}
        </div>

        <div class="gap-2 modal-footer justify-content-end">
          <button type="button" class="btn btn-outline-danger" id="cookieRejectBtn">
            Rifiuta
          </button>

          <a href="{{ url('/privacy-policy#cookies') }}" class="btn btn-outline-warning">
            Leggi policy
          </a>

          <button type="button" class="btn btn-success" id="cookieAcceptBtn">
            Accetta
          </button>
        </div>

      </div>
    </div>
  </div>

  <script>
  (function () {
    const CONSENT_COOKIE = 'sn_cookie_consent';
    const DAYS = {{ (int)($site?->cookie_consent_days ?? 180) }};

    function getCookie(name) {
      const m = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
      return m ? decodeURIComponent(m[1]) : null;
    }

    function setCookie(name, value, days) {
      const d = new Date();
      d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
      document.cookie = `${name}=${encodeURIComponent(value)}; expires=${d.toUTCString()}; path=/; SameSite=Lax`;
    }

    document.addEventListener('DOMContentLoaded', function () {
      // se già deciso (cookie), non mostrare
      const already = getCookie(CONSENT_COOKIE); // 'necessary' | 'all' | null
      if (already === 'necessary' || already === 'all') return;

      // bootstrap modal
      if (typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        console.error('[cookie] Bootstrap Modal non disponibile');
        return;
      }

      const modalEl = document.getElementById('cookieConsentModal');
      if (!modalEl) return;

      const modal = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
      modal.show();

      document.getElementById('cookieAcceptBtn')?.addEventListener('click', function () {
        setCookie(CONSENT_COOKIE, 'all', DAYS);
        modal.hide();
        window.location.reload();
      });

      document.getElementById('cookieRejectBtn')?.addEventListener('click', function () {
        setCookie(CONSENT_COOKIE, 'necessary', DAYS);
        modal.hide();
        window.location.reload();
      });
    });
  })();
</script>
@endif
