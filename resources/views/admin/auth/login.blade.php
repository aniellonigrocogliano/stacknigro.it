@extends('layouts.admin-auth')

@section('title', 'Login - Admin')

@section('content')
<main class="main-content  mt-0">
  <section>
<div class="page-header align-items-start min-vh-100"
     style="background-image: url('{{ asset('themes/admin/img/illustrations/login.avif') }}');">
      <span class="mask bg-gradient-dark opacity-6"></span>

      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">

              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">Login</h4>
                </div>
              </div>

              <div class="card-body">
                <form method="POST" action="{{ url('/login') }}" class="text-start">
                  @csrf

                  <div class="input-group input-group-outline my-3">
  <input
    type="email"
    name="email"
    class="form-control"
    placeholder="Email"
    value="{{ old('email') }}"
    required
  >
</div>
                  @error('email')
                    <div class="text-danger text-sm mt-n2 mb-2">{{ $message }}</div>
                  @enderror

<div class="input-group input-group-outline mb-3">
  <input
    type="password"
    name="password"
    class="form-control"
    placeholder="Password"
    required
  >
</div>
                  @error('password')
                    <div class="text-danger text-sm mt-n2 mb-2">{{ $message }}</div>
                  @enderror

                  <div class="form-check form-switch d-flex align-items-center mb-3">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label mb-0 ms-3" for="rememberMe">Ricordami</label>
                  </div>

                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Sign in</button>
                  </div>

                  {{-- NIENTE REGISTRAZIONE --}}
                  {{-- <p class="mt-4 text-sm text-center">Don&#39;t have an account? <a href="#" class="text-primary text-gradient font-weight-bold">Sign up</a></p> --}}
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>

      <footer class="footer position-absolute bottom-0 py-2 w-100">
        <div class="container">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 col-md-6 my-auto">
              <div class="copyright text-center text-sm text-white text-lg-start">
                © {{ date('Y') }} Stacknigro.it
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div>
  </section>
</main>
@endsection
