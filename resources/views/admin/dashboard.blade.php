@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="py-2 container-fluid">

  <div class="row">
    <div class="ms-3">
      <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
      <p class="mb-4">Panoramica sito: Inbox, Preventivi, Portfolio e contenuti.</p>
    </div>

    {{-- PRIMA RIGA: 4 KPI --}}
    <div class="row g-4">
      {{-- KPI 1 --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Inbox non letti</p>
                <h4 class="mb-0">{{ $inboxUnread }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-envelope"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              <a class="text-primary font-weight-bolder" href="{{ url('/admin/inbox') }}">Apri Inbox</a>
            </p>
          </div>
        </div>
      </div>

      {{-- KPI 2 --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Da rispondere</p>
                <h4 class="mb-0">{{ $inboxToReply }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-reply"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              Oggi: <span class="font-weight-bolder">{{ $inboxToday }}</span> messaggi
            </p>
          </div>
        </div>
      </div>

      {{-- KPI 3 --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Preventivi (30gg)</p>
                <h4 class="mb-0">{{ $quotes30 }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-receipt"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              Media: <span class="font-weight-bolder">
                {{ $quoteAvgTotal ? number_format($quoteAvgTotal, 2, ',', '.') . ' €' : '—' }}
              </span>
            </p>
          </div>
        </div>
      </div>

      {{-- KPI 4 --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Sito completato</p>
                <h4 class="mb-0">{{ $siteCompletion }}%</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-tasks"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              Progetti: <span class="font-weight-bolder">{{ $projectsPublished }}</span> pubb. /
              <span class="font-weight-bolder">{{ $projectsDraft }}</span> bozze
            </p>
          </div>
        </div>
      </div>
    </div>

    {{-- SECONDA RIGA: 4 KPI EXTRA --}}
    <div class="mt-1 row g-4">
      {{-- Skills Totali --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Skills totali</p>
                <h4 class="mb-0">{{ $skillsTotal ?? 0 }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-tools"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              <a class="text-primary font-weight-bolder" href="{{ url('/admin/skills') }}">Gestisci skills</a>
            </p>
          </div>
        </div>
      </div>

      {{-- Progetti Totali --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Progetti totali</p>
                <h4 class="mb-0">{{ $projectsTotal ?? 0 }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-folder-open"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              <a class="text-primary font-weight-bolder" href="{{ url('/admin/projects') }}">Gestisci progetti</a>
            </p>
          </div>
        </div>
      </div>

      {{-- Foto Progetti --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Foto progetti</p>
                <h4 class="mb-0">{{ $projectImagesTotal ?? 0 }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-images"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">
              Tot immagini caricate
            </p>
          </div>
        </div>
      </div>

      {{-- Visite --}}
      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="p-2 card-header ps-3">
            <div class="d-flex justify-content-between">
              <div>
                <p class="mb-0 text-sm text-capitalize">Visite</p>
                <h4 class="mb-0">{{ $visits ?? 0 }}</h4>
              </div>
              <div class="text-center shadow icon icon-md icon-shape bg-gradient-dark shadow-dark border-radius-lg">
                <i class="text-white fas fa-chart-line"></i>
              </div>
            </div>
          </div>
          <hr class="my-0 dark horizontal">
          <div class="p-2 card-footer ps-3">
            <p class="mb-0 text-sm">Placeholder (GA in arrivo)</p>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- CHARTS --}}
  <div class="mt-4 row g-4">
    <div class="col-lg-6 col-md-12">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Messaggi ultimi 7 giorni</h6>
          <p class="text-sm">Inbox + Preventivi ricevuti</p>
          <div class="pe-2">
            <div class="chart">
              <canvas id="chart-inbox" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="my-auto text-sm material-symbols-rounded me-1">schedule</i>
            <p class="mb-0 text-sm">schedule aggiornato adesso</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-6 col-md-12">
      <div class="card">
        <div class="card-body">
          <h6 class="mb-0">Preventivi ultimi 6 mesi</h6>
          <p class="text-sm">Numero richieste</p>
          <div class="pe-2">
            <div class="chart">
              <canvas id="chart-quotes" class="chart-canvas" height="170"></canvas>
            </div>
          </div>
          <hr class="dark horizontal">
          <div class="d-flex">
            <i class="my-auto text-sm material-symbols-rounded me-1">schedule</i>
            <p class="mb-0 text-sm">schedule max: {{ $quoteMaxTotal ? number_format($quoteMaxTotal, 2, ',', '.') . ' €' : '—' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- BOTTOM ROW: Attività recente a sinistra + (Ultimi messaggi + Checklist) a destra --}}
  <div class="mt-4 mb-4 row g-4">

    {{-- Attività recente (SINISTRA) --}}
    <div class="col-lg-4 col-md-6">
      <div class="card h-100">
        <div class="pb-0 card-header">
          <h6>Attività recente</h6>
          <p class="text-sm">
            <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
            <span class="font-weight-bold">{{ $quotes30 }}</span> preventivi negli ultimi 30gg
          </p>
        </div>
        <div class="p-3 card-body">
          <div class="timeline timeline-one-side">

            @foreach($lastQuotes as $q)
              @php
                $total = null;
                if (!empty($q->quote_payload)) {
                  $total = data_get($q->quote_payload, 'total');
                }
              @endphp
              <div class="mb-3 timeline-block">
                <span class="timeline-step">
                  <i class="fas fa-receipt text-info"></i>
                </span>
                <div class="timeline-content">
                  <h6 class="mb-0 text-sm text-dark font-weight-bold">
                    {{ $q->name }} — {{ $total ? number_format((float)$total, 0, ',', '.') . ' €' : 'Preventivo' }}
                  </h6>
                  <p class="mt-1 mb-0 text-xs text-secondary font-weight-bold">
                    {{ $q->created_at?->format('d/m/Y H:i') }}
                  </p>
                </div>
              </div>
            @endforeach

            <div class="timeline-block">
              <span class="timeline-step">
                <i class="fas fa-inbox text-danger"></i>
              </span>
              <div class="timeline-content">
                <h6 class="mb-0 text-sm text-dark font-weight-bold">Inbox</h6>
                <p class="mt-1 mb-0 text-xs text-secondary font-weight-bold">
                  <a class="text-primary" href="{{ url('/admin/inbox') }}">Vai a Inbox</a>
                  — Non letti: {{ $inboxUnread }}
                </p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- Destra: Ultimi messaggi + Checklist --}}
    <div class="col-lg-8 col-md-6">

      {{-- Ultimi messaggi --}}
      <div class="card">
        <div class="pb-0 card-header">
          <div class="row">
            <div class="col-lg-6 col-7">
              <h6>Ultimi messaggi</h6>
              <p class="mb-0 text-sm">
                <i class="fa fa-check text-info" aria-hidden="true"></i>
                <span class="font-weight-bold ms-1">{{ $inboxUnread }}</span> non letti
              </p>
            </div>
            <div class="my-auto col-lg-6 col-5 text-end">
              <a class="mb-0 btn btn-outline-primary btn-sm" href="{{ url('/admin/inbox') }}">
                Vai alla Inbox
              </a>
            </div>
          </div>
        </div>

        <div class="px-0 pb-2 card-body">
          <div class="table-responsive">
            <table class="table mb-0 align-items-center">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Da</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Oggetto</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data</th>
                </tr>
              </thead>
              <tbody>
                @foreach($lastMessages as $m)
                  @php
                    $isRead = !is_null($m->read_at);
                    $type = $m->source === 'quote' ? 'Preventivo' : 'Contatto';
                  @endphp
                  <tr style="cursor:pointer" onclick="window.location='{{ url('/admin/inbox/'.$m->id) }}'">
                    <td>
                      <div class="px-2 py-1 d-flex">
                        <div class="d-flex flex-column justify-content-center">
                          <h6 class="mb-0 text-sm {{ $isRead ? '' : 'fw-bold' }}">{{ $m->name }}</h6>
                          <p class="mb-0 text-xs text-secondary">{{ $m->email }}</p>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-xs font-weight-bold mb-0 {{ $isRead ? '' : 'fw-bold' }}">
                        {{ $m->subject ?: 'Senza oggetto' }}
                      </p>
                      <p class="mb-0 text-xs text-secondary">{{ \Illuminate\Support\Str::limit($m->user_message, 60) }}</p>
                    </td>
                    <td class="text-sm text-center align-middle">
                      <span class="badge bg-secondary">{{ $type }}</span>
                    </td>
                    <td class="text-center align-middle">
                      <span class="text-xs text-secondary font-weight-bold">{{ $m->created_at?->format('d/m/Y H:i') }}</span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Checklist contenuti sito (SISTEMATA) --}}
      <div class="mt-4 card">
        <div class="pb-0 card-header">
          <h6>Checklist contenuti sito</h6>
        </div>
        <div class="card-body">
          <div class="row">
            @foreach($checks as $label => $ok)
              <div class="mb-2 col-md-6">
                <div class="d-flex align-items-center">
                  <i class="{{ $ok ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger' }} me-2"></i>
                  <span class="text-sm">{{ $label }}</span>
                </div>
              </div>
            @endforeach
          </div>

          <hr class="dark horizontal">
          <p class="mb-0 text-sm">
            Skills: <span class="font-weight-bolder">{{ $skillsTotal ?? 0 }}</span> |
            Progetti: <span class="font-weight-bolder">{{ $projectsTotal ?? 0 }}</span> |
            Foto: <span class="font-weight-bolder">{{ $projectImagesTotal ?? 0 }}</span> |
            Visite: <span class="font-weight-bolder">{{ $visits ?? 0 }}</span>
          </p>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // Chart.js (assumo già incluso dal layout Material Dashboard)
  const inboxLabels = @json($chartInboxLabels);
  const inboxData = @json($chartInboxData);

  const quoteLabels = @json($chartQuoteLabels);
  const quoteData = @json($chartQuoteData);

  const ctxInbox = document.getElementById('chart-inbox')?.getContext('2d');
  if (ctxInbox && window.Chart) {
    new Chart(ctxInbox, {
      type: 'bar',
      data: {
        labels: inboxLabels,
        datasets: [{
          label: 'Messaggi',
          data: inboxData,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          barThickness: 'flex'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }

  const ctxQuotes = document.getElementById('chart-quotes')?.getContext('2d');
  if (ctxQuotes && window.Chart) {
    new Chart(ctxQuotes, {
      type: 'line',
      data: {
        labels: quoteLabels,
        datasets: [{
          label: 'Preventivi',
          data: quoteData,
          tension: 0.3,
          borderWidth: 2,
          pointRadius: 3,
          fill: false
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
  }
</script>
@endpush


