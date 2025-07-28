
@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <h4>Impostazioni Hero</h4>
            <form action="{{ url('/admin/hero') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group mb-3">
                    <label for="hero_name" class="form-label text-dark fw-bold">Nome Hero</label>
                    <input type="text" name="hero_name" id="hero_name"
                           class="form-control border border-dark ps-3"
                           value="{{ old('hero_name', $hero->hero_name ?? '') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="hero_subtitle" class="form-label text-dark fw-bold">Sottotitolo Hero</label>
                    <input type="text" name="hero_subtitle" id="hero_subtitle"
                           class="form-control border border-dark ps-3"
                           value="{{ old('hero_subtitle', $hero->hero_subtitle ?? '') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="hero_title" class="form-label text-dark fw-bold">Titolo Hero</label>
                    <input type="text" name="hero_title" id="hero_title"
                           class="form-control border border-dark ps-3"
                           value="{{ old('hero_title', $hero->hero_title ?? '') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="logo" class="form-label text-dark fw-bold">Logo</label>
                    @if($hero && $hero->logo)
                        <div class="mb-2">
                            <img src="{{ asset($hero->logo) }}" alt="Logo" style="max-height: 50px;">
                        </div>
                    @endif
                    <div class="input-group">
                        <input type="file" name="logo" class="form-control border border-dark" id="logo">
                        <label class="input-group-text btn btn-info text-white" for="logo">Upload</label>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="hero_image" class="form-label text-dark fw-bold">Immagine Hero</label>
                    @if($hero && $hero->hero_image)
                        <div class="mb-2">
                            <img src="{{ asset($hero->hero_image) }}" alt="Hero" style="max-height: 100px;">
                        </div>
                    @endif
                    <div class="input-group">
                        <input type="file" name="hero_image" class="form-control border border-dark" id="hero_image">
                        <label class="input-group-text btn btn-info text-white" for="hero_image">Upload</label>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg w-100 mt-4 mb-0">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
