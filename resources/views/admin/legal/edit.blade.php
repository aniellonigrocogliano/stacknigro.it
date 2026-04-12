@extends('layouts.admin')

@section('title', 'Policy & Cookie')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-scale-balanced me-2"></i>Policy & Cookie
    </h5>

    <button type="button"
            class="btn btn-success btn-sm js-confirm"
            data-title="Salva impostazioni"
            data-body="Confermi il salvataggio di Policy/Cookie/CAPTCHA/Banner/Analytics?"
            data-form="f-legal-save">
      <i class="fa-solid fa-floppy-disk me-1"></i> Salva
    </button>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form id="f-legal-save" method="POST" action="{{ route('legal.update') }}">
    @csrf
    @method('PUT')

    <div class="card">
      <div class="card-body">

        {{-- TABS --}}
        <div class="mb-3 nav-wrapper position-relative end-0">
          <ul class="p-1 nav nav-pills nav-fill" role="tablist">

            <li class="nav-item">
              <a class="px-0 py-1 mb-0 nav-link active"
                 data-bs-toggle="tab"
                 href="#tab-privacy"
                 role="tab"
                 aria-selected="true">
                <i class="mb-1 align-middle fa-solid fa-user-shield me-1"></i>
                Privacy
              </a>
            </li>

            <li class="nav-item">
              <a class="px-0 py-1 mb-0 nav-link"
                 data-bs-toggle="tab"
                 href="#tab-cookie"
                 role="tab"
                 aria-selected="false">
                <i class="mb-1 align-middle fa-solid fa-cookie-bite me-1"></i>
                Cookie
              </a>
            </li>

            {{-- ✅ NEW TAB CAPTCHA --}}
            <li class="nav-item">
              <a class="px-0 py-1 mb-0 nav-link"
                 data-bs-toggle="tab"
                 href="#tab-captcha"
                 role="tab"
                 aria-selected="false">
                <i class="mb-1 align-middle fa-solid fa-shield-halved me-1"></i>
                CAPTCHA
              </a>
            </li>

            <li class="nav-item">
              <a class="px-0 py-1 mb-0 nav-link"
                 data-bs-toggle="tab"
                 href="#tab-banner"
                 role="tab"
                 aria-selected="false">
                <i class="mb-1 align-middle fa-solid fa-bullhorn me-1"></i>
                Banner
              </a>
            </li>

            <li class="nav-item">
              <a class="px-0 py-1 mb-0 nav-link"
                 data-bs-toggle="tab"
                 href="#tab-analytics"
                 role="tab"
                 aria-selected="false">
                <i class="mb-1 align-middle fa-brands fa-google me-1"></i>
                Analytics
              </a>
            </li>

          </ul>
        </div>

        <div class="tab-content">

          {{-- PRIVACY --}}
          <div class="tab-pane fade show active" id="tab-privacy" role="tabpanel">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Titolo Privacy</label>
                <input type="text" name="privacy_title" class="form-control"
                       value="{{ old('privacy_title', $privacy->title) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Testo Privacy Policy</label>
                <textarea
                  id="privacy_editor"
                  name="privacy_content"
                  rows="14"
                  class="form-control"
                >{{ old('privacy_content', $privacy->content) }}</textarea>
              </div>
            </div>
          </div>

          {{-- COOKIE --}}
          <div class="tab-pane fade" id="tab-cookie" role="tabpanel">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Titolo Cookie</label>
                <input type="text" name="cookie_title" class="form-control"
                       value="{{ old('cookie_title', $cookie->title) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Testo Cookie Policy</label>
                <textarea
                  id="cookie_editor"
                  name="cookie_content"
                  rows="14"
                  class="form-control"
                >{{ old('cookie_content', $cookie->content) }}</textarea>
              </div>
            </div>
          </div>

          {{-- ✅ CAPTCHA --}}
          <div class="tab-pane fade" id="tab-captcha" role="tabpanel">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Titolo CAPTCHA</label>
                <input type="text" name="captcha_title" class="form-control"
                       value="{{ old('captcha_title', $captcha->title) }}">
              </div>

              <div class="col-12">
                <label class="form-label">Testo CAPTCHA Policy</label>
                <div class="mb-2 text-xs text-muted">
                  Questo testo verrà richiamato nel frontend con l’ancora <code>#captcha</code>.
                </div>
                <textarea
                  id="captcha_editor"
                  name="captcha_content"
                  rows="14"
                  class="form-control"
                >{{ old('captcha_content', $captcha->content) }}</textarea>
              </div>
            </div>
          </div>

          {{-- BANNER --}}
          <div class="tab-pane fade" id="tab-banner" role="tabpanel">
            <div class="row g-3">

              <div class="col-12">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox"
                         name="cookie_banner_enabled" id="cookie_banner_enabled"
                         @checked(old('cookie_banner_enabled', (bool)$site->cookie_banner_enabled))>
                  <label class="form-check-label" for="cookie_banner_enabled">
                    Abilita banner cookie
                  </label>
                </div>
              </div>

              <div class="col-12 col-lg-4">
                <label class="form-label">Durata consenso (giorni)</label>
                <input type="number" name="cookie_consent_days" class="form-control"
                       value="{{ old('cookie_consent_days', $site->cookie_consent_days ?? 180) }}">
              </div>

              <div class="col-12">
                <label class="form-label">HTML/Testo banner</label>
                <textarea name="cookie_banner_html" rows="8" class="form-control"
                          placeholder="Testo o HTML del banner (lo useremo in frontend)">{{ old('cookie_banner_html', $site->cookie_banner_html) }}</textarea>
                <div class="mt-2 text-xs text-muted">
                  Qui puoi mettere testo semplice o HTML. In frontend lo stamperemo nel banner.
                </div>
              </div>

            </div>
          </div>

          {{-- ANALYTICS --}}
          <div class="tab-pane fade" id="tab-analytics" role="tabpanel">
            <div class="row g-3">

              <div class="col-12 col-lg-4">
                <label class="form-label">Provider</label>
                <select name="analytics_provider" class="form-control">
                  @php $prov = old('analytics_provider', $site->analytics_provider); @endphp
                  <option value="" @selected(!$prov)>Nessuno</option>
                  <option value="ga4" @selected($prov==='ga4')>Google Analytics 4</option>
                </select>
              </div>

              <div class="col-12 col-lg-8">
                <label class="form-label">Measurement ID (G-XXXXXXX)</label>
                <input type="text" name="analytics_measurement_id" class="form-control"
                       value="{{ old('analytics_measurement_id', $site->analytics_measurement_id) }}"
                       placeholder="G-XXXXXXXXXX">
                <div class="mt-2 text-xs text-muted">
                  In frontend lo caricheremo solo dopo consenso cookie (quando implementiamo il banner).
                </div>
              </div>

            </div>
          </div>

        </div>{{-- /tab-content --}}

      </div>
    </div>
  </form>

  {{-- MODAL CONFERMA (no window.alert) --}}
  <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-normal" id="confirmActionTitle">Conferma</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="confirmActionBody">Sei sicuro?</div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Annulla</button>
          <button type="button" class="btn bg-gradient-danger" id="confirmActionSubmit">Conferma</button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ===== Modal conferma =====
  const modalEl = document.getElementById('confirmActionModal');
  if (modalEl && typeof bootstrap !== 'undefined') {
    const modal = new bootstrap.Modal(modalEl);
    const titleEl = document.getElementById('confirmActionTitle');
    const bodyEl  = document.getElementById('confirmActionBody');
    const submitBtn = document.getElementById('confirmActionSubmit');

    let pendingFormId = null;

    document.querySelectorAll('.js-confirm').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        pendingFormId = btn.getAttribute('data-form');
        titleEl.textContent = btn.getAttribute('data-title') || 'Conferma';
        bodyEl.textContent  = btn.getAttribute('data-body')  || 'Sei sicuro?';
        modal.show();
      });
    });

    submitBtn.addEventListener('click', () => {
      if (!pendingFormId) return;
      const form = document.getElementById(pendingFormId);
      if (form) form.submit();
    });

    modalEl.addEventListener('hidden.bs.modal', () => pendingFormId = null);
  }

  // ===== TinyMCE =====
  if (typeof tinymce === 'undefined') return;

  tinymce.remove('#privacy_editor');
  tinymce.remove('#cookie_editor');
  tinymce.remove('#captcha_editor');

  tinymce.init({
    selector: '#privacy_editor, #cookie_editor, #captcha_editor',
    height: 650,
    resize: true,
    menubar: true,
    license_key: 'gpl',
    language: 'it',
    language_url: "{{ asset('vendor/tinymce/langs/it.js') }}",

    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | moreTag | code fullscreen preview',

    images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open('POST', "{{ route('admin.tinymce.upload') }}");
      xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");

      xhr.upload.onprogress = (e) => {
        if (e.total > 0) progress((e.loaded / e.total) * 100);
      };

      xhr.onload = () => {
        if (xhr.status < 200 || xhr.status >= 300) return reject('Upload fallito: ' + xhr.status);
        let json = {};
        try { json = JSON.parse(xhr.responseText); } catch(e) {}
        if (!json.location) return reject('Risposta server senza location');
        resolve(json.location);
      };

      xhr.onerror = () => reject('Errore di rete');

      const formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());
      xhr.send(formData);
    }),

    setup: function (editor) {
      editor.ui.registry.addButton('moreTag', {
        text: 'MORE',
        onAction: () => editor.insertContent('\n<!--more-->\n')
      });
    }
  });
});
</script>
@endpush
