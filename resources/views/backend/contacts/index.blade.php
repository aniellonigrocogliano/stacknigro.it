@extends('backend.layouts.app')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h4>Recapiti</h4>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ url('/admin/contacts') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="email" class="form-label text-dark fw-bold">Email</label>
                    <input type="email" name="email" class="form-control border border-dark" value="{{ $contact->email ?? '' }}">
                </div>

                <div class="form-group mb-3">
                    <label for="pec" class="form-label text-dark fw-bold">PEC</label>
                    <input type="email" name="pec" class="form-control border border-dark" value="{{ $contact->pec ?? '' }}">
                </div>

                <div class="form-group mb-3">
                    <label for="phone" class="form-label text-dark fw-bold">Telefono</label>
                    <input type="text" name="phone" class="form-control border border-dark" value="{{ $contact->phone ?? '' }}">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Salva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection