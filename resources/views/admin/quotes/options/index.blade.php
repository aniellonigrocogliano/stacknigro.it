@extends('layouts.admin')

@section('title', 'Opzioni preventivo')

@section('content')
<div class="py-4 container-fluid">

  <div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
      <h4 class="mb-0">Opzioni preventivo</h4>
      <p class="mb-0 text-sm text-secondary">
        Qui crei le opzioni riutilizzabili (ore/prezzi). L’assegnazione ai livelli si fa dentro “Livelli”.
      </p>
    </div>

    <a href="{{ route('admin.quote-options.create') }}" class="mb-0 btn bg-gradient-dark">
      <i class="fa fa-plus me-2"></i> Nuova opzione
    </a>
  </div>

  @if (session('success'))
    <div class="text-white alert alert-success">{{ session('success') }}</div>
  @endif

  @if ($errors->any())
    <div class="text-white alert alert-danger">
      <strong>Ci sono errori:</strong>
      <ul class="mt-2 mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="card">
    <div class="card-header pb-0">
      <h6 class="mb-0">Elenco opzioni</h6>
      <p class="mb-0 text-sm text-secondary">
        Totali: <strong>{{ $options->count() }}</strong>
      </p>
    </div>

    <div class="card-body px-0 pb-2">
      <div class="table-responsive">
        <table class="table align-items-center mb-0">
          <thead>
            <tr>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Opzione</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ore</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Prezzo</th>
              <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Stato</th>
              <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Azioni</th>
            </tr>
          </thead>

          <tbody>
            @forelse($options as $opt)
              <tr>
                <td>
                  <div class="px-2 py-1">
                    <div class="text-sm fw-bold text-dark">{{ $opt->label }}</div>
                    @if(!empty($opt->help_text))
                      <div class="text-xs text-secondary">{{ $opt->help_text }}</div>
                    @endif
                  </div>
                </td>

                <td class="align-middle">
                  <span class="text-sm text-dark">
                    {{ (int)($opt->hours_min ?? 0) }}–{{ (int)($opt->hours_max ?? 0) }} h
                  </span>
                </td>

                <td class="align-middle">
                  @php
                    $pMin = $opt->price_min ?? null;
                    $pMax = $opt->price_max ?? null;
                  @endphp

                  @if($pMin !== null || $pMax !== null)
                    <span class="text-sm text-dark">
                      € {{ $pMin ?? '—' }} – {{ $pMax ?? '—' }}
                    </span>
                  @else
                    <span class="text-xs text-secondary">—</span>
                  @endif
                </td>

                <td class="align-middle">
                  @if((int)($opt->is_active ?? 1) === 1)
                    <span class="badge badge-sm bg-gradient-success">Attiva</span>
                  @else
                    <span class="badge badge-sm bg-gradient-secondary">Disattiva</span>
                  @endif
                </td>

                <td class="text-center align-middle">
                  <a href="{{ route('admin.quote-options.edit', $opt) }}" class="btn btn-link text-dark mb-0 px-2" title="Modifica">
                    <i class="fa fa-pen"></i>
                  </a>

                  <form action="{{ route('admin.quote-options.destroy', $opt) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-link text-danger mb-0 px-2" type="submit" title="Elimina"
                      onclick="return confirm('Eliminare questa opzione?')">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-4 text-center text-secondary">
                  Nessuna opzione inserita.
                </td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>
    </div>
  </div>

</div>
@endsection
