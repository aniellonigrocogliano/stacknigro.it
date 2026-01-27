<header id="header" class="header d-flex align-items-center fixed-top">
  <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

    <a href="{{ route('public.home') }}" class="logo d-flex align-items-center">
      {{-- nel template c'è <h1 class="sitename">Bootslander</h1> --}}
      {{-- noi mettiamo il logo dinamico se esiste, altrimenti testo --}}
      @if($site?->logo_path)
        <img
          src="{{ asset('storage/'.$site->logo_path) }}"
          alt="logo"
          style="height: 28px; width: auto;"
        >
      @endif

      <h1 class="sitename ms-2">
        {{ $site?->site_name ?? 'Stacknigro.it' }}
      </h1>
    </a>

    <nav id="navmenu" class="navmenu">
<ul>
  <li>
    <a href="{{ route('public.home') }}"
       class="{{ request()->routeIs('public.home') ? 'active' : '' }}">
      <i class="fa-solid fa-house me-2"></i> Home
    </a>
  </li>

  <li>
    <a href="{{ route('public.bio') }}"
       class="{{ request()->routeIs('public.bio') ? 'active' : '' }}">
      <i class="fa-solid fa-user me-2"></i> Chi sono
    </a>
  </li>

  <li>
    <a href="{{ route('public.skills') }}"
       class="{{ request()->routeIs('public.skills') ? 'active' : '' }}">
      <i class="fa-solid fa-screwdriver-wrench me-2"></i> Skills
    </a>
  </li>

  <li>
    <a href="{{ route('public.projects.index') }}"
       class="{{ request()->routeIs('public.projects.index') ? 'active' : '' }}">
      <i class="fa-solid fa-folder-open me-2"></i> Mei progetti
    </a>
  </li>

  <li>
    <a href="{{ route('public.quotes') }}"
       class="{{ request()->routeIs('public.quotes') ? 'active' : '' }}">
      <i class="fa-solid fa-file-invoice-dollar me-2"></i> Preventivo
    </a>
  </li>

  <li>
    <a href="{{ route('public.contacts') }}"
       class="{{ request()->routeIs('public.contacts') ? 'active' : '' }}">
      <i class="fa-solid fa-envelope me-2"></i> Contattami
    </a>
  </li>

  <li>
    <a href="{{ route('privacy.policy') }}"
       class="{{ request()->routeIs('privacy.policy') ? 'active' : '' }}">
      <i class="fa-solid fa-shield-halved me-2"></i> Privacy policy
    </a>
  </li>
</ul>

      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

  </div>
</header>


