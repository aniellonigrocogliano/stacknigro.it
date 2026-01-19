@extends('layouts.admin')

@section('title', 'Aggiungi Skill')

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card">
      <div class="pb-0 card-header">
        <h6>Aggiungi skill</h6>
      </div>

      <div class="card-body">
        <form method="POST" action="{{ route('admin.skills.store') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name') <div class="mt-1 text-xs text-danger">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Colore (es: #00c853)</label>
            <input type="text" name="color" class="form-control" value="{{ old('color') }}">
            @error('color') <div class="mt-1 text-xs text-danger">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Icona FontAwesome (es: fa-solid fa-code)</label>
            <input type="text" name="fa_icon" class="form-control" value="{{ old('fa_icon') }}">
            @error('fa_icon') <div class="mt-1 text-xs text-danger">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Descrizione</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description') <div class="mt-1 text-xs text-danger">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="form-label">Ordinamento (sort)</label>
            <input type="number" name="sort" class="form-control" value="{{ old('sort', 0) }}" min="0">
            @error('sort') <div class="mt-1 text-xs text-danger">{{ $message }}</div> @enderror
          </div>

          <button class="btn bg-gradient-dark" type="submit">Salva</button>
          <a href="{{ route('admin.skills.index') }}" class="btn btn-outline-dark ms-2">Annulla</a>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
