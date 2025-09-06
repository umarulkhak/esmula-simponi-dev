<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('operator.beranda') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" id="Wallet">
                    <path d="M28,7H4a3,3,0,0,0-3,3V28a3,3,0,0,0,3,3H28a3,3,0,0,0,3-3V23H27a4,4,0,0,1,0-8h4V10A3,3,0,0,0,28,7ZM10,27H6a1,1,0,0,1,0-2h4a1,1,0,0,1,0,2Z" fill="#696cff"></path>
                    <path d="M25 19a2 2 0 0 0 2 2h4V17H27A2 2 0 0 0 25 19zM14 1.29a1 1 0 0 0-1.41 0L8.9 5h8.82zM23.1 5L19.39 1.29a1 1 0 0 0-1.41 0l-.57.57L20.55 5z" fill="#696cff"></path>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Simponi</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ \Route::is('operator.beranda') ? 'active' : '' }}">
            <a href="{{ route('operator.beranda') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Beranda</div>
            </a>
        </li>

        <!-- Data User -->
        <li class="menu-item {{ \Route::is('user.*') ? 'active' : '' }}">
            <a href="{{ route('user.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div>Data User</div>
            </a>
        </li>

        <!-- Data Wali -->
        <li class="menu-item {{ \Route::is('wali.*') ? 'active' : '' }}">
            <a href="{{ route('wali.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>Data Wali Murid</div>
            </a>
        </li>

        <!-- Data Siswa -->
        <li class="menu-item {{ \Route::is('siswa.*') ? 'active' : '' }}">
            <a href="{{ route('siswa.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div>Data Siswa</div>
            </a>
        </li>

        <!-- Data Biaya -->
        <li class="menu-item {{ \Route::is('biaya.*') ? 'active' : '' }}">
            <a href="{{ route('biaya.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-coin-stack"></i>
                <div>Data Biaya</div>
            </a>
        </li>

        <!-- Data Tagihan -->
        <li class="menu-item {{ \Route::is('tagihan.*') ? 'active' : '' }}">
            <a href="{{ route('tagihan.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-receipt"></i>
                <div>Data Tagihan</div>
            </a>
        </li>

        <!-- Logout -->
        <li class="menu-item">
            <a href="{{ route('logout') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-power-off me-2"></i>
                <div>Logout</div>
            </a>
        </li>
    </ul>
</aside>
