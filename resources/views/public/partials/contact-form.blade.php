@php
  $action = $action ?? url('/contatti');
  $source = $source ?? 'contact';

  $quotePayload  = $quotePayload ?? null;
  $quoteSummary  = $quoteSummary ?? null;
  $mode          = $mode ?? 'send';

  // nuovi flag (default: mostra tutto)
  $hideSubmit = $hideSubmit ?? false;
  $hidePrivacy = $hidePrivacy ?? false;
@endphp

<form action="{{ $action }}" method="POST" class="php-email-form">
  @csrf

  <input type="hidden" name="source" value="{{ $source }}">
  <input type="hidden" name="mode" value="{{ $mode }}">

  {{-- hidden preventivo --}}
  @if(!is_null($quotePayload))
    <input type="hidden" name="quote_payload" value="{{ $quotePayload }}">
  @endif
  @if(!is_null($quoteSummary))
    <input type="hidden" name="quote_summary" value="{{ $quoteSummary }}">
  @endif

  <div class="row gy-4">
    <div class="col-md-6">
      <label class="form-label">Nome *</label>
      <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      @error('name') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
      <label class="form-label">Email *</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      @error('email') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
      <label class="form-label">Telefono</label>
      <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
      @error('phone') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
      <label class="form-label">Oggetto</label>
      <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
      @error('subject') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
      <label class="form-label">Come mi hai trovato?</label>
      <select name="how_found" class="form-select">
        <option value="">Seleziona</option>
        <option value="google"   @selected(old('how_found')==='google')>Google</option>
        <option value="social"   @selected(old('how_found')==='social')>Social</option>
        <option value="referral" @selected(old('how_found')==='referral')>Passaparola</option>
        <option value="other"    @selected(old('how_found')==='other')>Altro</option>
      </select>
      @error('how_found') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
      <label class="form-label">Messaggio *</label>
      <textarea name="user_message" class="form-control" rows="6" required>{{ old('user_message') }}</textarea>
      @error('user_message') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
    </div>

    {{-- PRIVACY (opzionale) --}}
    @unless($hidePrivacy)
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="privacy_accepted" id="privacy_accepted" value="1" required>
          <label class="form-check-label" for="privacy_accepted">
            Ho letto e accetto la <a href="{{ url('/privacy-policy#privacy') }}">Privacy Policy</a>.
          </label>
        </div>
        @error('privacy_accepted') <div class="mt-1 text-danger small">{{ $message }}</div> @enderror
      </div>
    @endunless

    {{-- SUBMIT (opzionale) --}}
    @unless($hideSubmit)
      <div class="col-12">
        <button type="submit" class="btn btn-primary">
          Invia messaggio
        </button>
      </div>
    @endunless
  </div>
</form>
