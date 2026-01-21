<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>@yield('title', 'Admin')</title>

  <link href="{{ asset('themes/admin/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link href="{{ asset('themes/admin/css/material-dashboard.min.css') }}" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
  <link rel="icon" type="image/png"
        href="{{ $site?->favicon_path ? asset('storage/'.$site->favicon_path) : asset('themes/bootslander/img/favicon.png') }}">
</head>

<body class="bg-gray-200 g-sidenav-show">

  {{-- SIDEBAR --}}
  @include('admin.partials.sidebar')

  {{-- CONTENUTO --}}
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @yield('content')
  </main>

  {{-- MODAL GLOBALE AVVISI (SUCCESS / ERROR) --}}
  <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title font-weight-normal" id="alertModalTitle">Avviso</h5>
          <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="alertModalBody"></div>
        <div class="modal-footer">
          <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">
            Chiudi
          </button>
        </div>
      </div>
    </div>
  </div>

  {{-- CORE JS (SOLO QUI, NON NEL LOGIN) --}}
  <script src="{{ asset('themes/admin/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('themes/admin/js/material-dashboard.min.js') }}"></script>

  {{-- AUTO-OPEN MODAL DA SESSION --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modalEl = document.getElementById('alertModal');
      if (!modalEl || typeof bootstrap === 'undefined') return;

      const modal = new bootstrap.Modal(modalEl);

      @if(session('success'))
        document.getElementById('alertModalTitle').innerText = 'Operazione completata';
        document.getElementById('alertModalBody').innerText = @json(session('success'));
        modal.show();
      @endif

      @if(session('error'))
        document.getElementById('alertModalTitle').innerText = 'Errore';
        document.getElementById('alertModalBody').innerText = @json(session('error'));
        modal.show();
      @endif
    });
  </script>

  @stack('scripts')
</body>
</html>
