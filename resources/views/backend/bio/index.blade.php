@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ url('/admin/bio') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="content" class="form-label fw-bold">Contenuto Bio</label>
            <textarea name="content" id="editor" class="form-control" rows="10">{{ old('content', $bio->content ?? '') }}</textarea>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="btn btn-success">Salva Bio</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false,
        plugins: 'lists link image preview code fullscreen',
        toolbar: 'undo redo | bold italic | bullist numlist | link image | preview code fullscreen'
    });
</script>
@endpush