@php
  $action = $action ?? url('/contatti');
  $source = $source ?? 'contact';
  $quotePayload = $quotePayload ?? null;
  $quoteSummary = $quoteSummary ?? null;
  $mode = $mode ?? 'send';
  $hideSubmit = $hideSubmit ?? false;
  $hidePrivacy = $hidePrivacy ?? false;

  // Campi per la gestione errori inline di Laravel
  $inlineFields = ['name','email','phone','subject','how_found','user_message','privacy_accepted','snct','sncs'];
@endphp

<form action="{{ $action }}" method="POST" class="php-email-form" id="mainContactForm">
  @csrf
  <input type="hidden" name="source" value="{{ $source }}">
  <input type="hidden" name="mode" value="{{ $mode }}">

  @if(!is_null($quotePayload))
    <input type="hidden" name="quote_payload" value="{{ $quotePayload }}">
  @endif
  @if(!is_null($quoteSummary))
    <input type="hidden" name="quote_summary" value="{{ $quoteSummary }}">
  @endif

  <div class="row gy-4">
    {{-- NOME --}}
    <div class="col-md-6">
      <label class="form-label fw-bold">Nome *</label>
      <input type="text" name="name"
             class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}" required
             onfocus="trackField('name')">
      @error('name') <div class="invalid-feedback d-block small text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- EMAIL --}}
    <div class="col-md-6">
      <label class="form-label fw-bold">Email *</label>
      <input type="email" name="email"
             class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email') }}" required
             onfocus="trackField('email')">
      @error('email') <div class="invalid-feedback d-block small text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- TELEFONO --}}
    <div class="col-md-6">
      <label class="form-label fw-bold">Telefono</label>
      <input type="text" name="phone"
             class="form-control @error('phone') is-invalid @enderror"
             value="{{ old('phone') }}"
             placeholder="Es: +39 333 1234567"
             onfocus="trackField('phone')">
      <div class="form-text small">Usato solo per chiarimenti tecnici.</div>
      @error('phone') <div class="invalid-feedback d-block small text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- OGGETTO --}}
    <div class="col-md-6">
      <label class="form-label fw-bold">Oggetto</label>
      <input type="text" name="subject"
             class="form-control @error('subject') is-invalid @enderror"
             value="{{ old('subject') }}"
             onfocus="trackField('subject')">
      @error('subject') <div class="invalid-feedback d-block small text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- COME TROVATO --}}
    <div class="col-md-6">
      <label class="form-label fw-bold">Come mi hai trovato?</label>
      <select name="how_found" class="form-select" onfocus="trackField('how_found')">
        <option value="">Seleziona un'opzione</option>
        <option value="google" @selected(old('how_found') === 'google')>Google</option>
        <option value="social" @selected(old('how_found') === 'social')>Social</option>
        <option value="referral" @selected(old('how_found') === 'referral')>Passaparola</option>
        <option value="other" @selected(old('how_found') === 'other')>Altro</option>
      </select>
    </div>

    {{-- MESSAGGIO --}}
    <div class="col-12">
      <label class="form-label fw-bold">Messaggio *</label>
      <textarea name="user_message"
                class="form-control @error('user_message') is-invalid @enderror"
                rows="5" required
                onfocus="trackField('message')">{{ old('user_message') }}</textarea>
      @error('user_message') <div class="invalid-feedback d-block small text-danger">{{ $message }}</div> @enderror
    </div>

    {{-- PRIVACY --}}
    @unless($hidePrivacy)
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input @error('privacy_accepted') is-invalid @enderror"
                 type="checkbox" name="privacy_accepted" id="privacy_accepted"
                 value="1" required {{ old('privacy_accepted') ? 'checked' : '' }}>
          <label class="form-check-label small" for="privacy_accepted">
            Ho letto e accetto la <a href="{{ url('/privacy-policy#privacy') }}" target="_blank">Privacy Policy</a>.
          </label>
        </div>
      </div>
    @endunless

    {{-- CAPTCHA STACKNIGRO --}}
    <div class="col-12">
      <div class="sn-captcha" data-sitekey="sn_y1bsbkwrlfntoawzkefm86op3ddd8rm5" data-theme="standard"></div>
      @error('snct') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- SUBMIT --}}
    @unless($hideSubmit)
      <div class="col-12">
        <button type="submit" class="btn btn-primary px-4 py-2" id="btnSubmitForm">
          <i class="bi bi-send me-2"></i> Invia messaggio
        </button>
      </div>
    @endunless
  </div>
</form>

@once
  @push('scripts')
    <script src="https://captcha.stacknigro.it/widget.js" async></script>
    <script>
      /**
       * Analytics Intelligence per il Form
       */
      function trackField(fieldName) {
          if (typeof gtag === 'function') {
              gtag('event', 'form_interaction', {
                  'field_name': fieldName,
                  'source': '{{ $source }}'
              });
          }
      }

      // Cattura l'errore del tooltip nativo (quando il browser blocca l'invio)
      document.querySelectorAll('#mainContactForm input, #mainContactForm textarea, #mainContactForm select').forEach(el => {
          el.addEventListener('invalid', function() {
              if (typeof gtag === 'function') {
                  gtag('event', 'form_validation_error', {
                      'field_name': this.name,
                      'source': '{{ $source }}'
                  });
              }
          });
      });

      // Tracciamento tentativo di invio finale
      document.getElementById('mainContactForm').addEventListener('submit', function() {
          if (typeof gtag === 'function') {
              gtag('event', 'form_submit_click', { 'source': '{{ $source }}' });
          }
      });
    </script>
  @endpush
@endonce
