@php
    use App\Models\InboxConversation;

    $inboxUnread = InboxConversation::query()
        ->whereNull('deleted_at')
        ->whereNull('archived_at')
        ->whereNull('read_at')
        ->count();
@endphp

<aside class="my-2 bg-white sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2"
       id="sidenav-main">

  <div class="sidenav-header">
    <i class="top-0 p-3 cursor-pointer fas fa-times text-dark opacity-5 position-absolute end-0 d-none d-xl-none"
       aria-hidden="true"
       id="iconSidenav"></i>

    <a class="px-4 py-3 m-0 navbar-brand" href="{{ url('/admin') }}">
<img src="{{ $site?->logo_path ? asset('storage/'.$site->logo_path) : asset('themes/admin/img/logo-ct-dark.png') }}"
     class="navbar-brand-img"
     width="26"
     height="26"
     alt="logo">
      <span class="text-sm ms-1 text-dark">Stacknigro Admin</span>
    </a>
  </div>

  <hr class="mt-0 mb-2 horizontal dark">

  <div class="w-auto collapse navbar-collapse" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- DASHBOARD --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin') ? 'active' : '' }}"
           href="{{ url('/admin') }}">
          <i class="fa-solid fa-gauge-high opacity-5"></i>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      {{-- HERO + LOGO --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin/hero*') ? 'active' : '' }}"
           href="{{ url('/admin/hero') }}">
          <i class="fa-solid fa-image opacity-5"></i>
          <span class="nav-link-text ms-1">Hero + Logo</span>
        </a>
      </li>

      {{-- BIO --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin/bio*') ? 'active' : '' }}"
           href="{{ url('/admin/bio') }}">
          <i class="fa-solid fa-user opacity-5"></i>
          <span class="nav-link-text ms-1">Bio</span>
        </a>
      </li>

      {{-- SKILLS --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin/skills*') ? 'active' : '' }}"
           href="{{ url('/admin/skills') }}">
          <i class="fa-solid fa-bolt opacity-5"></i>
          <span class="nav-link-text ms-1">Skills</span>
        </a>
      </li>

      {{-- CONTATTI --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin/contacts*') ? 'active' : '' }}"
           href="{{ url('/admin/contacts') }}">
          <i class="fa-solid fa-address-book opacity-5"></i>
          <span class="nav-link-text ms-1">Contatti</span>
        </a>
      </li>

      {{-- PROGETTI --}}
      <li class="nav-item">
        <a class="nav-link text-dark {{ request()->is('admin/projects*') ? 'active' : '' }}"
           href="{{ url('/admin/projects') }}">
          <i class="fa-solid fa-briefcase opacity-5"></i>
          <span class="nav-link-text ms-1">Progetti</span>
        </a>
      </li>

{{-- PREVENTIVI --}}

<li class="nav-item">
  <a class="nav-link text-dark {{ request()->is('admin/quotes*') ? 'active' : '' }}"
     href="{{ url('/admin/quotes') }}">
    <i class="fa-solid fa-file-invoice-dollar opacity-5"></i>
    <span class="nav-link-text ms-1">Preventivi</span>
  </a>
</li>

{{-- INBOX --}}
<li class="nav-item">
  <a class="nav-link text-dark {{ request()->is('admin/inbox*') ? 'active' : '' }}"
     href="{{ url('/admin/inbox') }}">
    <i class="fa-solid fa-inbox opacity-5"></i>
    <span class="nav-link-text ms-1">Inbox</span>

    @if($inboxUnread > 0)
      <span class="badge bg-info ms-auto">{{ $inboxUnread }}</span>
    @endif
  </a>
</li>
{{-- POLICY & COOKIE --}}
<li class="nav-item">
  <a class="nav-link text-dark {{ request()->is('admin/legal*') ? 'active' : '' }}"
     href="{{ url('/admin/legal') }}">
    <i class="fa-solid fa-scale-balanced opacity-5"></i>
    <span class="nav-link-text ms-1">Policy & Cookie</span>
  </a>
</li>

      {{-- ACCOUNT --}}
      <li class="mt-3 nav-item">
        <h6 class="text-xs ps-4 ms-2 text-uppercase text-dark font-weight-bolder opacity-5">
          Account
        </h6>
      </li>

      {{-- LOGOUT --}}
      <li class="nav-item">
        <form method="POST" action="{{ url('/logout') }}">
          @csrf
          <button type="submit" class="bg-transparent border-0 nav-link text-dark w-100 text-start">
            <i class="fa-solid fa-right-from-bracket opacity-5"></i>
            <span class="nav-link-text ms-1">Logout</span>
          </button>
        </form>
      </li>

    </ul>
  </div>

</aside>
