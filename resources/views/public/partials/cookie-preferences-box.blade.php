@php
  $consent = $_COOKIE['sn_cookie_consent'] ?? null;
  $days = (int)($site?->cookie_consent_days ?? 180);
@endphp

<div class="border card">
  <div class="card-body">
    <div class="flex-wrap gap-2 d-flex align-items-start justify-content-between">
      <div>
        <h3 class="mb-1 h6"><i class="fa-solid fa-sliders me-2"></i>Preferenze cookie</h3>
        <p class="mb-0 text-muted">Puoi scegliere o cambiare in qualsiasi momento.</p>
      </div>

      @if($consent)
        <span class="badge bg-success align-self-start">
          Attuale: {{ $consent === 'all' ? 'Tutti' : 'Solo necessari' }}
        </span>
      @else
        <span class="badge bg-warning text-dark align-self-start">
          Nessuna scelta salvata
        </span>
      @endif
    </div>

    <form class="mt-3">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="sn_consent_choice"
               id="snConsentNecessary_{{ uniqid() }}" value="necessary"
               {{ $consent === 'necessary' ? 'checked' : '' }}>
        <label class="form-check-label">
          Accetto solo i cookie necessari
        </label>
      </div>

      <div class="mt-2 form-check">
        <input class="form-check-input" type="radio" name="sn_consent_choice"
               id="snConsentAll_{{ uniqid() }}" value="all"
               {{ $consent === 'all' ? 'checked' : '' }}>
        <label class="form-check-label">
          Accetto tutti i cookie (inclusi analytics)
        </label>
      </div>

      <div class="flex-wrap gap-2 mt-3 d-flex">
        <button type="button" class="btn btn-success sn-consent-save">
          <i class="fa-solid fa-floppy-disk me-1"></i> Salva
        </button>

        @if($consent)
          <button type="button" class="btn btn-outline-danger sn-consent-reset">
            <i class="fa-solid fa-trash-arrow-up me-1"></i> Reset cookie sito
          </button>
        @endif
      </div>

      <div class="mt-2 text-muted small">Durata consenso: {{ $days }} giorni.</div>
    </form>
  </div>
</div>

@push('scripts')
<script>
(function () {
  const CONSENT_COOKIE = 'sn_cookie_consent';
  const DAYS = {{ $days }};

  function setCookie(name, value, days) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${d.toUTCString()}; path=/; SameSite=Lax`;
  }

  function deleteCookie(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax`;
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sn-consent-save').forEach(btn => {
      btn.addEventListener('click', () => {
        const form = btn.closest('form');
        const chosen = form?.querySelector('input[name="sn_consent_choice"]:checked')?.value;
        if (!chosen) return;
        setCookie(CONSENT_COOKIE, chosen, DAYS);
        window.location.reload();
      });
    });

    document.querySelectorAll('.sn-consent-reset').forEach(btn => {
      btn.addEventListener('click', () => {
        deleteCookie(CONSENT_COOKIE);
        try { localStorage.removeItem(CONSENT_COOKIE); } catch(e) {}
        window.location.reload();
      });
    });
  });
})();
</script>
@endpush
