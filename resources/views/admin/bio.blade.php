@extends('layouts.admin')

@section('content')
<div class="py-4 container-fluid">
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Bio</h5>
      <p class="mb-0 text-sm">
        Scrivi la tua bio. Inserisci <code>&lt;!--more--&gt;</code> dove vuoi troncare l’anteprima in Home.
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

      <form method="POST" action="{{ route('admin.bio.update') }}">
        @csrf

        <textarea id="bio" name="bio">{!! old('bio', $settings->bio) !!}</textarea>

        <button class="mt-3 btn bg-gradient-dark" type="submit">Salva</button>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.remove('#bio');

tinymce.init({
  selector: '#bio',
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
