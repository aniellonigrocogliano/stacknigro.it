@extends('layouts.admin')

@section('title', 'Aggiungi Skill')

@section('content')
<div class="py-3 container-fluid">

  <div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="fa-solid fa-screwdriver-wrench me-2"></i>Aggiungi skill
    </h5>
  </div>

  <div class="card">
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
@endsection
