<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- ✅ Tambahkan mb-4 di sini untuk jarak ke menu -->
    <div class="app-brand demo mb-4">
        <a href="{{ route('wali.beranda') }}" class="app-brand-link d-flex align-items-start">
            <!-- Logo -->
            <span class="app-brand-logo demo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" id="Wallet">
                    <path d="M28,7H4a3,3,0,0,0-3,3V28a3,3,0,0,0,3,3H28a3,3,0,0,0,3-3V23H27a4,4,0,0,1,0-8h4V10A3,3,0,0,0,28,7ZM10,27H6a1,1,0,0,1,0-2h4a1,1,0,0,1,0,2Z" fill="#696cff"></path>
                    <path d="M25 19a2 2 0 0 0 2 2h4V17H27A2 2 0 0 0 25 19zM14 1.29a1 1 0 0 0-1.41 0L8.9 5h8.82zM23.1 5L19.39 1.29a1 1 0 0 0-1.41 0l-.57.57L20.55 5z" fill="#696cff"></path>
                </svg>
            </span>

            <!-- Container untuk Judul + Subjudul -->
            <div class="ms-3">
                <div class="app-brand-text demo menu-text fw-bolder">Simponi</div>
                <div class="text-muted small mt-1">SMP Muhammadiyah Larangan</div>
            </div>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ \Route::is('wali.beranda') ? 'active' : '' }}">
            <a href="{{ route('wali.beranda') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Beranda</div>
            </a>
        </li>
        <!-- Data Anak -->
        <li class="menu-item {{ \Route::is('wali.siswa.*') ? 'active' : '' }}">
            <a href="{{ route('wali.siswa.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>Data Anak</div>
            </a>
        </li>
        <!-- Data Tagihan Anak-->
        <li class="menu-item {{ \Route::is('wali.tagihan.*') ? 'active' : '' }}">
            <a href="{{ route('wali.tagihan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>Data Tagihan</div>
            </a>
        </li>
        <!-- Logout -->
        <li class="menu-item">
            <a href="{{ route('logoutwali') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-power-off me-2"></i>
                <div>Logout</div>
            </a>
        </li>
    </ul>
</aside>
