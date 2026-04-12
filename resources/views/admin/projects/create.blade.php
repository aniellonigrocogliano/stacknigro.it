@extends('layouts.admin')

@section('title', 'Nuovo progetto')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <h4 class="mb-0">Nuovo progetto</h4>

    <a href="{{ route('admin.projects.index') }}" class="mb-0 btn btn-outline-dark">
      <i class="fa-solid fa-arrow-left me-2"></i> Indietro
    </a>
  </div>

  {{-- Errori validazione --}}
  @if ($errors->any())
    <div class="text-white alert alert-danger">
      <strong>Ci sono errori nel form:</strong>
      <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data" id="projectCreateForm">
    @csrf

    <div class="row">
      {{-- COL SINISTRA --}}
      <div class="col-lg-8">
        <div class="card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Dati progetto</h6>
            <p class="mb-0 text-sm text-secondary">Titolo, descrizioni e contenuto completo.</p>
          </div>

          <div class="card-body">
            {{-- Titolo --}}
            <div class="mb-3">
              <label class="form-label">Titolo</label>
              <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title') }}"
                placeholder="Es. Portfolio Laravel"
                required
              >
            </div>

            {{-- Descrizione breve (FIX: excerpt) --}}
            <div class="mb-3">
              <label class="form-label">Descrizione breve (Home)</label>
              <textarea
                name="excerpt"
                class="form-control"
                rows="3"
                placeholder="2-3 righe da mostrare in Home"
              >{{ old('excerpt') }}</textarea>
            </div>

            {{-- Descrizione lunga (FIX: body) --}}
            <div class="mb-2">
              <label class="form-label">Descrizione lunga (pagina progetto)</label>
              <textarea
                id="long_description"
                name="body"
                class="form-control"
              >{{ old('body') }}</textarea>
              <small class="mt-2 text-muted d-block">
                Puoi inserire immagini nel testo (upload interno). Se vuoi puoi usare <code>&lt;!--more--&gt;</code> dove preferisci (se ti serve in futuro).
              </small>
            </div>
          </div>
        </div>
      </div>

      {{-- COL DESTRA --}}
      <div class="mt-4 col-lg-4 mt-lg-0">
        <div class="card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Pubblicazione & Media</h6>
          </div>

          <div class="card-body">

            {{-- Pubblicato --}}
            <div class="mb-4 d-flex align-items-center justify-content-between">
              <div>
                <label class="mb-0 form-label">Pubblicato</label>
                <div class="text-xs text-secondary">Mostra il progetto sul sito</div>
              </div>

              <div class="m-0 form-check form-switch">
                <input
                  class="form-check-input"
                  type="checkbox"
                  role="switch"
                  id="is_published"
                  name="is_published"
                  value="1"
                  {{ old('is_published') ? 'checked' : '' }}
                >
              </div>
            </div>

            {{-- Immagini --}}
            <div class="mb-2">
              <label class="form-label">Immagini (multiple)</label>

              <input
                type="file"
                name="images[]"
                id="images"
                class="form-control"
                multiple
                accept="image/*"
              >

              <small id="imagesCounter" class="mt-2 text-muted d-block">
                Nessun file selezionato
              </small>

<small class="mt-2 text-xs text-secondary d-block">
  Limiti:
  <strong>Server</strong>
  (POST {{ ini_get('post_max_size') }},
  Upload {{ ini_get('upload_max_filesize') }})
  —
  <strong>Laravel</strong>
  ({{ number_format($maxKb / 1024, 1) }} MB per immagine)
</small>
            </div>

          </div>
        </div>

        {{-- Salva --}}
        <div class="mt-4 card">
          <div class="card-body">
            <button type="submit" class="mb-0 btn btn-success w-100">
              <i class="fa-solid fa-floppy-disk me-2"></i> Salva progetto
            </button>
          </div>
        </div>

      </div>
    </div>
  </form>

</div>
@endsection

@push('scripts')
  {{-- TinyMCE --}}
  <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

  <script>
    // CONTATORE IMMAGINI MULTIPLE
    const input = document.getElementById('images');
    const counter = document.getElementById('imagesCounter');

    function formatSize(bytes){
      const mb = bytes / (1024*1024);
      return mb.toFixed(2) + ' MB';
    }

    input?.addEventListener('change', () => {
      const files = Array.from(input.files || []);
      if (!files.length) {
        counter.textContent = 'Nessun file selezionato';
        return;
      }

      const total = files.reduce((sum,f) => sum + (f.size || 0), 0);
      const names = files.map(f => `${f.name} (${formatSize(f.size || 0)})`).join(', ');

      counter.textContent = `${files.length} file selezionati — Totale: ${formatSize(total)} — ${names}`;
    });

    // blindatura: prima del submit, forza TinyMCE a scrivere nel textarea
    document.getElementById('projectCreateForm')?.addEventListener('submit', () => {
      if (window.tinymce) tinymce.triggerSave();
    });

   // TinyMCE su BODY
tinymce.remove('#body');

tinymce.init({
  selector: '#body',
  height: 350,
  resize: true,
  menubar: true,
  license_key: 'gpl',

  language: 'it',
  language_url: "{{ asset('vendor/tinymce/langs/it.js') }}",

  plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
  toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media table | moreTag | code fullscreen preview',

  // ✅ PERMETTE FONT AWESOME (icone)
  extended_valid_elements: 'i[class|style|aria-hidden],span[class|style|aria-hidden]',
  custom_elements: 'i,span',
  verify_html: false,

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
