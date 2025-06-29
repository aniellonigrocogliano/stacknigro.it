
@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-lg-6 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Prima Card</h5>
          <p class="card-text">Questa è una card di esempio per verificare lo stile Material Dashboard.</p>
          <a href="#" class="btn btn-primary">Azione</a>
        </div>
      </div>
    </div>
    <div class="col-lg-6 col-sm-6 mb-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Seconda Card</h5>
          <p class="card-text">Un'altra card per confermare che i CSS sono correttamente caricati.</p>
          <a href="#" class="btn btn-success">Conferma</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
