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
tinymce.init({
  selector: '#bio',
  height: 520,
  menubar: true,
  license_key: 'gpl',
  plugins: 'link lists code table autoresize',
  toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link table | moreTag | code',
  branding: false,
  setup: function (editor) {
    editor.ui.registry.addButton('moreTag', {
      text: 'MORE',
      onAction: function () {
        editor.insertContent('\n<!--more-->\n');
      }
    });
  }
});
</script>
@endpush
