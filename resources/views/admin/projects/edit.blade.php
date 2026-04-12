@extends('layouts.admin')

@section('title', 'Modifica progetto')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Modifica progetto</h4>
      <p class="mb-0 text-sm text-secondary">Aggiorna contenuti e gestisci le immagini.</p>
    </div>

    <a href="{{ route('admin.projects.index') }}" class="mb-0 btn btn-outline-dark">
      <i class="fa-solid fa-arrow-left me-2"></i> Indietro
    </a>
  </div>

  @if (session('success'))
    <div class="text-white alert alert-success">{{ session('success') }}</div>
  @endif

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

  <form method="POST" action="{{ route('admin.projects.update', $project) }}" enctype="multipart/form-data" id="projectEditForm">
    @csrf
    @method('PUT')

    <div class="row">
      {{-- COL SINISTRA --}}
      <div class="col-lg-8">

        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Dati progetto</h6>
          </div>

          <div class="card-body">
            {{-- Titolo --}}
            <div class="mb-3">
              <label class="form-label">Titolo</label>
              <input
                type="text"
                name="title"
                class="form-control"
                value="{{ old('title', $project->title) }}"
                required
              >
            </div>

            {{-- Descrizione breve --}}
            <div class="mb-3">
              <label class="form-label">Descrizione breve (Home)</label>
              <textarea
                name="excerpt"
                class="form-control"
                rows="3"
              >{{ old('excerpt', $project->excerpt) }}</textarea>
            </div>

            {{-- Descrizione lunga --}}
            <div class="mb-2">
              <label class="form-label">Descrizione lunga (pagina progetto)</label>
              <textarea
                id="body"
                name="body"
                class="form-control"
              >{{ old('body', $project->body) }}</textarea>

              <small class="mt-2 text-muted d-block">
                Puoi inserire immagini nel testo (upload interno).
              </small>
            </div>
          </div>
        </div>

        {{-- IMMAGINI GIA' CARICATE --}}
        <div class="card">
          <div class="pb-0 card-header d-flex align-items-center justify-content-between">
            <div>
              <h6 class="mb-0">Immagini già caricate</h6>
              <p class="mb-0 text-sm text-secondary">
                Totale: <strong>{{ $project->images->count() }}</strong>
                <span id="sortStatus" class="text-xs ms-2"></span>
              </p>
            </div>

            <small class="text-xs text-secondary">
              Trascina per riordinare (la cover resta evidenziata).
            </small>
          </div>

          <div class="card-body">
            @if ($project->images->isEmpty())
              <div class="text-sm text-secondary">Nessuna immagine caricata.</div>
            @else

              {{-- ✅ AGGIUNTO: id per drag&drop --}}
              <div class="row" id="imagesGrid">
                @foreach ($project->images as $img)
                  @php $isCover = ((int) $img->is_cover === 1); @endphp

                  {{-- ✅ AGGIUNTO: data-id + draggable --}}
                  <div class="mb-3 col-md-6 col-lg-4"
                       data-id="{{ $img->id }}"
                       draggable="{{ $isCover ? 'false' : 'true' }}">

                    <div class="p-2 border rounded-3 h-100 position-relative {{ $isCover ? 'border-warning' : '' }}"
                         style="{{ $isCover ? 'border-width:2px !important;' : '' }}">

                      {{-- STELLINA COVER (DORATA) --}}
                      @if($isCover)
                        <span class="top-0 m-2 position-absolute start-0" title="Cover" style="z-index: 5;">
                          <i class="fa-solid fa-star fa-lg"
                             style="color:#f6c343; text-shadow:0 1px 2px rgba(0,0,0,.25);"></i>
                        </span>
                      @endif

                      <div class="mb-2 overflow-hidden ratio ratio-4x3 rounded-2 bg-light">
                        <img
                          src="{{ asset('storage/'.$img->path) }}"
                          alt="immagine progetto"
                          style="object-fit: cover; width: 100%; height: 100%;"
                        >
                      </div>

                      <div class="d-flex align-items-start justify-content-between">
                        <div class="pe-2">
                          <div class="text-sm fw-bold text-dark" style="word-break: break-word;">
                            {{ basename($img->path) }}
                          </div>
                          <div class="text-xs text-secondary">
                            {{ $img->created_at?->format('d/m/Y H:i') }}
                          </div>
                        </div>

                        <div class="gap-1 d-flex">
                          {{-- IMPOSTA COVER --}}
                          @if(!$isCover)
                            {{-- ✅ AGGIUNTO: classe per intercettare e fare reload immediato --}}
                            <form method="POST"
                                  action="{{ route('admin.project-images.cover', ['project' => $project, 'image' => $img]) }}"
                                  class="js-set-cover">
                              @csrf
                              <button type="submit" class="mb-0 btn btn-sm btn-outline-dark" title="Imposta come cover">
                                <i class="fa-regular fa-star"></i>
                              </button>
                            </form>
                          @endif

                          {{-- ELIMINA --}}
                          <button
                            type="button"
                            class="mb-0 btn btn-sm btn-outline-danger"
                            title="Elimina"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal"
                            data-action="{{ route('admin.project-images.destroy', [
    'project' => $project->id,
    'image' => $img->id
]) }}"
                            data-title="Eliminare questa immagine?"
                            data-body="Vuoi eliminare '{{ basename($img->path) }}'?"
                          >
                            <i class="fa-solid fa-trash"></i>
                          </button>

                          {{-- ✅ AGGIUNTO: maniglia drag solo visiva (non cover) --}}
                          @if(!$isCover)
                            <button type="button"
                                    class="mb-0 btn btn-sm btn-outline-secondary"
                                    title="Trascina per ordinare"
                                    style="cursor: grab;">
                              <i class="fa-solid fa-grip-vertical"></i>
                            </button>
                          @endif
                        </div>

                      </div>
                    </div>
                  </div>
                @endforeach
              </div>

            @endif
          </div>
        </div>

      </div>

      {{-- COL DESTRA --}}
      <div class="mt-4 col-lg-4 mt-lg-0">

        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Pubblicazione</h6>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
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
                  {{ old('is_published', $project->is_published) ? 'checked' : '' }}
                >
              </div>
            </div>
          </div>
        </div>

        {{-- AGGIUNGI NUOVE IMMAGINI --}}
        <div class="mb-4 card">
          <div class="pb-0 card-header">
            <h6 class="mb-0">Aggiungi nuove immagini</h6>
          </div>

          <div class="card-body">
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
  ({{ number_format(($maxKb ?? 5120) / 1024, 1) }} MB per immagine)
</small>
          </div>
        </div>

        {{-- SALVA --}}
        <div class="card">
          <div class="card-body">
            <button type="submit" class="mb-0 btn btn-success w-100">
              <i class="fa-solid fa-floppy-disk me-2"></i> Salva modifiche
            </button>
          </div>
        </div>

      </div>
    </div>
  </form>

</div>

{{-- MODAL (stesso stile skills) --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-normal" id="confirmDeleteTitle">Conferma eliminazione</h5>
        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="confirmDeleteBody">Sei sicuro?</div>
      <div class="modal-footer">
        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Annulla</button>
        <form method="POST" id="confirmDeleteForm">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn bg-gradient-danger">Elimina</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>

  <script>
    // contatore immagini
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

    // modal delete
    document.addEventListener('DOMContentLoaded', () => {
      const modalEl = document.getElementById('confirmDeleteModal');
      if (!modalEl) return;

      modalEl.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) return;

        document.getElementById('confirmDeleteForm').action = button.getAttribute('data-action');
        document.getElementById('confirmDeleteTitle').innerText = button.getAttribute('data-title') || 'Conferma eliminazione';
        document.getElementById('confirmDeleteBody').innerText = button.getAttribute('data-body') || 'Sei sicuro?';
      });
    });

    // sync TinyMCE -> textarea prima del submit
    document.getElementById('projectEditForm')?.addEventListener('submit', () => {
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
    // =========================
    // ✅ Cover: refresh immediato
    // =========================
    document.querySelectorAll('.js-set-cover').forEach((form) => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
          const res = await fetch(form.action, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'Accept': 'application/json'
            }
          });

          // anche se la rotta risponde con redirect, qui non ci interessa:
          // dopo il POST ricarichiamo e vediamo subito la stellina aggiornata
          window.location.reload();
        } catch (err) {
          console.error(err);
          // fallback: submit normale
          form.submit();
        }
      });
    });

    // =========================
    // ✅ Drag&Drop: salva ordine (solo non-cover)
    // =========================
    const grid = document.getElementById('imagesGrid');
    const statusEl = document.getElementById('sortStatus');
    let dragEl = null;

    function setStatus(text, kind = 'secondary') {
      if (!statusEl) return;
      statusEl.className = 'ms-2 text-xs text-' + kind;
      statusEl.textContent = text || '';
    }

    function getOrderedIdsForSave() {
      // inviamo SOLO la sequenza completa: cover prima (se presente), poi gli altri come sono nel DOM
      const ids = [];
      const items = Array.from(grid?.querySelectorAll('[data-id]') || []);
      for (const el of items) {
        const id = el.getAttribute('data-id');
        if (id) ids.push(parseInt(id, 10));
      }
      return ids;
    }

    async function saveOrder() {
      // rotta che devi avere: admin.project-images.sort
      const url = "{{ route('admin.project-images.sort', $project) }}";
      const ids = getOrderedIdsForSave();

      setStatus('Salvataggio ordine...', 'secondary');

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}",
            'Accept': 'application/json'
          },
          body: JSON.stringify({ ids })
        });

        if (!res.ok) throw new Error('HTTP ' + res.status);

        setStatus('Ordine salvato ✓', 'success');
        setTimeout(() => setStatus(''), 1500);
      } catch (e) {
        console.error(e);
        setStatus('Errore salvataggio ordine', 'danger');
      }
    }

    function makeDraggable(col) {
      const isDraggable = col.getAttribute('draggable') === 'true';
      if (!isDraggable) return;

      col.addEventListener('dragstart', (e) => {
        dragEl = col;
        col.classList.add('opacity-50');
        e.dataTransfer.effectAllowed = 'move';
      });

      col.addEventListener('dragend', () => {
        col.classList.remove('opacity-50');
        dragEl = null;
      });

      col.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
      });

      col.addEventListener('drop', async (e) => {
        e.preventDefault();
        if (!dragEl || dragEl === col) return;

        const rect = col.getBoundingClientRect();
        const isAfter = (e.clientY - rect.top) > (rect.height / 2);

        if (isAfter) col.after(dragEl);
        else col.before(dragEl);

        await saveOrder();
      });
    }

    if (grid) {
      Array.from(grid.children).forEach(makeDraggable);
    }
  </script>
@endpush
