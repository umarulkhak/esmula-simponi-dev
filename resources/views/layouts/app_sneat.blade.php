<!DOCTYPE html>
<!--
==========================================================
 SNEAT - Bootstrap 5 HTML Admin Template
==========================================================
 Penulis     : Umar Ulkhak
 Tujuan      : Layout utama aplikasi Simponi (Operator Dashboard)
 Fitur       :
   - Responsive layout (mobile/desktop)
   - Sidebar menu dinamis (highlight active route)
   - Navbar dengan search & user dropdown
   - Flash message & error handling
   - Integrasi Select2, Masking Rupiah, Font Awesome
   - Footer & copyright otomatis

 Dokumentasi:
   - Gunakan @section('content') di view turunan
   - Gunakan @push('scripts') untuk script khusus halaman
   - Asset disimpan di folder /public/sneat/
==========================================================
-->
<html
  lang="id"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}"
  data-template="vertical-menu-template-free"
>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="description" content="Sistem Informasi Pembayaran Online (Simponi) - Dashboard Operator" />

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/wallet.svg') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('font/css/all.min.css') }}">

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>

    <!-- Custom CSS -->
    <style>
        /* Custom style untuk avatar */
        .avatar.bg-primary {
            background-color: #696cff !important;
        }
    </style>
</head>

<body>
    <!-- Layout Wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            <!-- Sidebar Menu -->
            @include('layouts.partials.sidebar')

            <!-- Main Content -->
            <div class="layout-page">

                <!-- Navbar -->
                @include('layouts.partials.navbar')

                <!-- Content Wrapper -->
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <!-- Flash Messages -->
                        @include('flash::message')

                        <!-- Error Messages -->
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6 class="alert-heading fw-bold mb-1">Terjadi Kesalahan!</h6>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Main Content -->
                        @yield('content')

                    </div>

                    <!-- Footer -->
                    @include('layouts.partials.footer')

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content Wrapper -->

            </div>
            <!-- / Layout Page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout Wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('sneat/assets/js/dashboards-analytics.js') }}"></script>

    <!-- Third Party JS -->
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js') }}"></script>

    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: "Pilih..."
            });

            // Initialize Rupiah Masking
            $('.rupiah').mask("#.##0", {
                reverse: true
            });

            // Auto dismiss flash messages
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>

    <!-- Stack Scripts -->
    @stack('scripts')
</body>
</html>
