<!DOCTYPE html>
<!--
|--------------------------------------------------------------------------
| VIEW: Login Page
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Halaman login untuk sistem Simponi SMP Muhammadiyah Larangan
| Fitur       :
|   - Logo gambar (PNG/JPG) di tengah
|   - Teks branding di bawahnya
|   - Toggle password berfungsi
|   - Clean code, responsif, dokumentasi jelas
-->
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Login - Simponi SMP Muhammadiyah Larangan</title>
    <meta name="description" content="Sistem Informasi Pembayaran Online SMP Muhammadiyah Larangan" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/logo-fav.png') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
  </head>

  <body>
    <!-- Content -->
    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Login Card -->
          <div class="card">
            <div class="card-body">
              <!-- Logo — CENTERED IMAGE + TEXT BELOW -->
              <div class="text-center mb-4">
                <!-- Gambar Logo Sekolah -->
                <img
                  src="{{ asset('sneat/assets/img/logo-esmula.png') }}"
                  alt="Logo SMP Muhammadiyah Larangan"
                  style="width: 100px; height: auto; border-radius: 10px;"
                />

                <!-- Teks Branding -->
                <h5 class="fw-bold mt-3 mb-2">Simponi SMP Muhammadiyah Larangan</h5>
              </div>
              <!-- /Logo -->

              <h6 class="text-center mb-3">Selamat Datang!</h6>
              <p class="text-center mb-4">Silakan login untuk mengakses sistem</p>

              <!-- Form Login -->
              <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="text"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    required
                    autofocus
                  />
                  @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    @if (Route::has('password.request'))
                      <a href="{{ route('password.request') }}">
                        <small>Lupa Password?</small>
                      </a>
                    @endif
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control @error('password') is-invalid @enderror"
                      name="password"
                      placeholder="••••••••••••"
                      required
                    />
                    <span class="input-group-text cursor-pointer" id="togglePassword">
                      <i class="bx bx-hide"></i>
                    </span>
                  </div>
                  @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }} />
                    <label class="form-check-label" for="remember"> Ingat Saya </label>
                  </div>
                </div>

                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                </div>
              </form>

              <!-- Register Link -->
              @if (Route::has('register'))
                <p class="text-center">
                  <span>Belum punya akun?</span>
                  <a href="{{ route('register') }}">
                    <span>Buat Akun</span>
                  </a>
                </p>
              @endif
            </div>
          </div>
          <!-- /Login Card -->
        </div>
      </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    {{-- <!-- Toggle Password Visibility -->
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
          const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
          password.setAttribute('type', type);
          this.querySelector('i').classList.toggle('bx-hide');
          this.querySelector('i').classList.toggle('bx-show');
        });
      });
    </script> --}}
  </body>
</html>
