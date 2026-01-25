@extends('layouts.admin')

@section('content')
<div class="py-4 container-fluid">

  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Privacy & Cookie</h5>
      <p class="mb-0 text-sm">
        Gestisci i testi di <strong>Privacy Policy</strong>, <strong>Cookie Policy</strong> e il contenuto del <strong>banner cookie</strong>.
      </p>
    </div>

    <div class="card-body">

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

      <form method="POST" action="{{ route('admin.policies.update') }}">
        @csrf
        @method('PUT')

        {{-- Tabs --}}
        <ul class="p-1 bg-gray-100 nav nav-pills nav-fill border-radius-lg" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-privacy-tab" data-bs-toggle="tab" data-bs-target="#tab-privacy" type="button" role="tab">
              <i class="fa-solid fa-user-shield me-1"></i> Privacy Policy
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-cookie-tab" data-bs-toggle="tab" data-bs-target="#tab-cookie" type="button" role="tab">
              <i class="fa-solid fa-cookie-bite me-1"></i> Cookie Policy
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-banner-tab" data-bs-toggle="tab" data-bs-target="#tab-banner" type="button" role="tab">
              <i class="fa-solid fa-bullhorn me-1"></i> Banner Cookie
            </button>
          </li>
        </ul>

        <div class="mt-3 tab-content">

          {{-- PRIVACY --}}
          <div class="tab-pane fade show active" id="tab-privacy" role="tabpanel" aria-labelledby="tab-privacy-tab">
            <div class="mb-2">
              <label class="mb-1 form-label">Testo Privacy Policy</label>
              <p class="mb-2 text-sm text-secondary">
                Qui puoi inserire anche intestazioni, elenchi, link, ecc.
              </p>
              <textarea id="privacy_policy" class="tinymce" name="privacy_policy">{!! old('privacy_policy', $settings->privacy_policy ?? '') !!}</textarea>
            </div>
          </div>

          {{-- COOKIE --}}
          <div class="tab-pane fade" id="tab-cookie" role="tabpanel" aria-labelledby="tab-cookie-tab">
            <div class="mb-2">
              <label class="mb-1 form-label">Testo Cookie Policy</label>
              <p class="mb-2 text-sm text-secondary">
                Puoi descrivere cookie necessari, statistiche, terze parti, durata, ecc.
              </p>
              <textarea id="cookie_policy" class="tinymce" name="cookie_policy">{!! old('cookie_policy', $settings->cookie_policy ?? '') !!}</textarea>
            </div>
          </div>

          {{-- BANNER --}}
          <div class="tab-pane fade" id="tab-banner" role="tabpanel" aria-labelledby="tab-banner-tab">
            <div class="mb-2">
              <label class="mb-1 form-label">Contenuto Banner Cookie (modal)</label>
              <p class="mb-2 text-sm text-secondary">
                Qui scrivi <strong>il testo mostrato nel banner</strong>. Se vuoi puoi mettere link/ancore alla pagina policy.
              </p>
              <textarea id="cookie_banner" class="tinymce" name="cookie_banner">{!! old('cookie_banner', $settings->cookie_banner ?? '') !!}</textarea>

              <div class="mt-3">
                <label class="mb-1 form-label">Data ultimo aggiornamento (solo informativa)</label>
                <div class="input-group input-group-outline {{ !empty($settings->policies_updated_at) ? 'is-filled' : '' }}">
                  <label class="form-label">Aggiornato il</label>
                  <input
                    type="date"
                    name="policies_updated_at"
                    class="form-control"
                    value="{{ old('policies_updated_at', optional($settings->policies_updated_at)->format('Y-m-d')) }}"
                  >
                </div>
                <p class="mt-1 mb-0 text-xs text-secondary">
                  Se la lasci vuota, non mostriamo la data nel frontend.
                </p>
              </div>

            </div>
          </div>

        </div>

        <button class="mt-3 btn bg-gradient-dark" type="submit">
          Salva
        </button>

      </form>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
  // pulizia eventuali init precedenti
  tinymce.remove('.tinymce');

  tinymce.init({
    selector: '.tinymce',
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
        if (xhr.status < 200 || xhr.status >= 300) {
          reject('Upload fallito: ' + xhr.status);
          return;
        }
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
</script>
@endpush
