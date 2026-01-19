@extends('layouts.admin')

@section('title', 'Hero + Logo')

@section('content')
<div class="py-4 container-fluid">

  @if(session('success'))
    <div class="text-white alert alert-success">
      {{ session('success') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="text-white alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="pb-0 card-header">
      <h6 class="mb-0">Hero + Logo</h6>
      <p class="mb-0 text-sm">Qui modifichi titolo, sottotitolo e carichi un solo logo che aggiorna anche la favicon.</p>
    </div>
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
    <div class="card-body">
      <form method="POST" action="{{ url('/admin/hero') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label class="form-label">Titolo (riga grande)</label>
          <input type="text" name="hero_title" class="form-control"
                 value="{{ old('hero_title', $settings->hero_title) }}">
        </div>

        <div class="mb-3">
          <label class="form-label">Sottotitolo (riga sotto)</label>
          <input type="text" name="hero_subtitle" class="form-control"
                 value="{{ old('hero_subtitle', $settings->hero_subtitle) }}">
        </div>

        <div class="mb-3">
          <label class="form-label">Logo (PNG consigliato)</label>
          <input type="file" name="logo" class="form-control">
          <small class="text-muted">Il logo viene usato per frontend, backend e genera favicon 32x32.</small>
        </div>

        @php
          $logoUrl = $settings->logo_path ? asset('storage/'.$settings->logo_path) : null;
          $favUrl  = $settings->favicon_path ? asset('storage/'.$settings->favicon_path) : null;
        @endphp

        @if($logoUrl)
          <div class="mb-3">
            <div class="gap-3 d-flex align-items-center">
              <div>
                <p class="mb-1 text-sm">Logo attuale</p>
                <img src="{{ $logoUrl }}" style="height:60px;">
              </div>
              @if($favUrl)
                <div>
                  <p class="mb-1 text-sm">Favicon 32x32</p>
                  <img src="{{ $favUrl }}" style="height:32px; width:32px;">
                </div>
              @endif
            </div>
          </div>
        @endif

        <button type="submit" class="mb-0 btn bg-gradient-dark">Salva</button>
      </form>
    </div>
  </div>

</div>
@endsection
