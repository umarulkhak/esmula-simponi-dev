<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Wali Murid - SMP Muhammadiyah Larangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .active-menu { background-color: #030213; color: white; }
        .card { background: white; border-radius: 0.625rem; border: 1px solid rgba(0, 0, 0, 0.1); }
        .badge-success { background-color: #22c55e; color: white; }
        .badge-danger { background-color: #ef4444; color: white; }
        .badge-warning { background-color: #f59e0b; color: white; }
        .badge-secondary { background-color: #6b7280; color: white; }
        .content-section { display: none; }
        .content-section.active { display: block; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center">
                        <span class="text-white font-medium">SP</span>
                    </div>
                    <h1 class="text-xl font-medium">Portal Wali Murid</h1>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button class="relative p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-gray-600 text-sm font-medium">BU</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-sm font-medium">Budi Santoso</p>
                        <p class="text-xs text-gray-500">Wali Murid</p>
                    </div>
                </div>

                <button class="p-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 h-screen">
            <div class="p-4">
                <div class="space-y-1">
                    <button onclick="showSection('dashboard')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100 active-menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </button>
                    <button onclick="showSection('tagihan')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Tagihan SPP
                    </button>
                    <button onclick="showSection('kehadiran')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Kehadiran
                    </button>
                    <button onclick="showSection('nilai')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Rapor & Nilai
                    </button>
                    <button onclick="showSection('profil')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profil Siswa
                    </button>
                    <button onclick="showSection('pengaturan')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Pengaturan
                    </button>
                    <button onclick="showSection('bantuan')" class="menu-btn w-full flex items-center gap-3 px-3 py-2 rounded-lg text-left hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Bantuan
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="max-w-7xl mx-auto">
                <!-- Dashboard Section -->
                <div id="dashboard" class="content-section active">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-2xl font-semibold mb-1">Dashboard</h2>
                            <p class="text-gray-500">Selamat datang di Portal Wali Murid SMP Muhammadiyah Larangan</p>
                        </div>

                        <!-- Overview Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="card p-6">
                                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <h3 class="text-sm font-medium">Tagihan SPP</h3>
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-2xl font-semibold">Rp 200.000</div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge-danger px-2 py-1 rounded text-xs">Belum Lunas</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Jatuh tempo: 10 September 2025</p>
                                </div>
                            </div>

                            <div class="card p-6">
                                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <h3 class="text-sm font-medium">Kehadiran Bulan Ini</h3>
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-2xl font-semibold">18/20</div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge-secondary px-2 py-1 rounded text-xs">90% Hadir</span>
                                    </div>
                                    <p class="text-xs text-gray-500">2 hari tidak hadir</p>
                                </div>
                            </div>

                            <div class="card p-6">
                                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <h3 class="text-sm font-medium">Nilai Rata-rata</h3>
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-2xl font-semibold">85.4</div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Baik</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Semester ini</p>
                                </div>
                            </div>

                            <div class="card p-6">
                                <div class="flex flex-row items-center justify-between space-y-0 pb-2">
                                    <h3 class="text-sm font-medium">Kegiatan Ekstrakurikuler</h3>
                                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-2xl font-semibold">3</div>
                                    <div class="flex items-center gap-2">
                                        <span class="border border-gray-300 px-2 py-1 rounded text-xs">Aktif</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Pramuka, Basket, Bahasa Inggris</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                            <!-- Billing Table -->
                            <div class="card">
                                <div class="p-6 pb-4">
                                    <h3 class="text-lg font-semibold">Riwayat Tagihan SPP</h3>
                                </div>
                                <div class="px-6 pb-6">
                                    <div class="overflow-x-auto">
                                        <table class="w-full">
                                            <thead>
                                                <tr class="border-b border-gray-200">
                                                    <th class="text-left py-3 px-2 text-sm font-medium">Bulan</th>
                                                    <th class="text-left py-3 px-2 text-sm font-medium">Jumlah</th>
                                                    <th class="text-left py-3 px-2 text-sm font-medium">Jatuh Tempo</th>
                                                    <th class="text-left py-3 px-2 text-sm font-medium">Status</th>
                                                    <th class="text-left py-3 px-2 text-sm font-medium">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 px-2 text-sm">Juli 2025</td>
                                                    <td class="py-3 px-2 text-sm font-medium">Rp 100.000</td>
                                                    <td class="py-3 px-2 text-sm text-gray-500">10 Agu 2025</td>
                                                    <td class="py-3 px-2">
                                                        <span class="badge-danger px-2 py-1 rounded text-xs">Belum Lunas</span>
                                                    </td>
                                                    <td class="py-3 px-2">
                                                        <button class="p-1 text-gray-500 hover:text-gray-700">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 px-2 text-sm">Juni 2025</td>
                                                    <td class="py-3 px-2 text-sm font-medium">Rp 100.000</td>
                                                    <td class="py-3 px-2 text-sm text-gray-500">10 Jul 2025</td>
                                                    <td class="py-3 px-2">
                                                        <span class="badge-success px-2 py-1 rounded text-xs">Lunas</span>
                                                    </td>
                                                    <td class="py-3 px-2">
                                                        <div class="flex gap-2">
                                                            <button class="p-1 text-gray-500 hover:text-gray-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                            </button>
                                                            <button class="p-1 text-gray-500 hover:text-gray-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr class="border-b border-gray-100">
                                                    <td class="py-3 px-2 text-sm">Mei 2025</td>
                                                    <td class="py-3 px-2 text-sm font-medium">Rp 100.000</td>
                                                    <td class="py-3 px-2 text-sm text-gray-500">10 Jun 2025</td>
                                                    <td class="py-3 px-2">
                                                        <span class="badge-success px-2 py-1 rounded text-xs">Lunas</span>
                                                    </td>
                                                    <td class="py-3 px-2">
                                                        <div class="flex gap-2">
                                                            <button class="p-1 text-gray-500 hover:text-gray-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                                </svg>
                                                            </button>
                                                            <button class="p-1 text-gray-500 hover:text-gray-700">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Announcements -->
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Informasi Terbaru</h3>
                                <div class="space-y-6">
                                    <div class="card">
                                        <div class="p-6 pb-4">
                                            <h4 class="font-semibold flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                                </svg>
                                                Pengumuman Sekolah
                                            </h4>
                                        </div>
                                        <div class="px-6 pb-6 space-y-4">
                                            <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium">Rapat Orang Tua Siswa</h5>
                                                        <p class="text-sm text-gray-500">15 Ags 2025</p>
                                                    </div>
                                                    <span class="badge-danger px-2 py-1 rounded text-xs">Penting</span>
                                                </div>
                                                <p class="text-sm leading-relaxed">Mengundang seluruh orang tua siswa untuk menghadiri rapat koordinasi semester baru yang akan dilaksanakan pada hari Sabtu, 17 Agustus 2025 pukul 09.00 WIB di Aula sekolah.</p>
                                            </div>
                                            <div class="border border-gray-200 rounded-lg p-4 space-y-3">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium">Libur Hari Kemerdekaan RI</h5>
                                                        <p class="text-sm text-gray-500">14 Ags 2025</p>
                                                    </div>
                                                    <span class="badge-secondary px-2 py-1 rounded text-xs">Info</span>
                                                </div>
                                                <p class="text-sm leading-relaxed">Sekolah diliburkan pada tanggal 17 Agustus 2025 dalam rangka memperingati HUT RI ke-80. Masuk kembali pada tanggal 19 Agustus 2025.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tagihan Section -->
                <div id="tagihan" class="content-section">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-2xl font-semibold mb-1">Tagihan SPP</h2>
                            <p class="text-gray-500">Kelola dan pantau pembayaran SPP anak Anda</p>
                        </div>
                        <div class="card">
                            <div class="p-6 pb-4">
                                <h3 class="text-lg font-semibold">Riwayat Tagihan SPP</h3>
                            </div>
                            <div class="px-6 pb-6">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-gray-200">
                                                <th class="text-left py-3 px-2 text-sm font-medium">Bulan</th>
                                                <th class="text-left py-3 px-2 text-sm font-medium">Jumlah</th>
                                                <th class="text-left py-3 px-2 text-sm font-medium">Jatuh Tempo</th>
                                                <th class="text-left py-3 px-2 text-sm font-medium">Status</th>
                                                <th class="text-left py-3 px-2 text-sm font-medium">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="border-b border-gray-100">
                                                <td class="py-3 px-2 text-sm">Januari 2025</td>
                                                <td class="py-3 px-2 text-sm font-medium">Rp 850.000</td>
                                                <td class="py-3 px-2 text-sm text-gray-500">10 Jan 2025</td>
                                                <td class="py-3 px-2">
                                                    <span class="badge-danger px-2 py-1 rounded text-xs">Belum Lunas</span>
                                                </td>
                                                <td class="py-3 px-2">
                                                    <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Bayar</button>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-gray-100">
                                                <td class="py-3 px-2 text-sm">Desember 2024</td>
                                                <td class="py-3 px-2 text-sm font-medium">Rp 850.000</td>
                                                <td class="py-3 px-2 text-sm text-gray-500">10 Des 2024</td>
                                                <td class="py-3 px-2">
                                                    <span class="badge-success px-2 py-1 rounded text-xs">Lunas</span>
                                                </td>
                                                <td class="py-3 px-2">
                                                    <button class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">Download</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kehadiran Section -->
                <div id="kehadiran" class="content-section">
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-2xl font-semibold mb-1">Kehadiran</h2>
                            <p class="text-gray-500">Pantau kehadiran dan rekap absensi anak Anda</p>
                        </div>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Statistik Kehadiran -->
                            <div class="card">
                                <div class="p-6 pb-4">
                                    <h3 class="font-semibold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Statistik Kehadiran
                                    </h3>
                                </div>
                                <div class="px-6 pb-6 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center p-4 bg-green-50 rounded-lg">
                                            <div class="text-2xl font-semibold text-green-700">18</div>
                                            <div class="text-sm text-green-600">Hari Hadir</div>
                                        </div>
                                        <div class="text-center p-4 bg-red-50 rounded-lg">
                                            <div class="text-2xl font-semibold text-red-700">2</div>
                                            <div class="text-sm text-red-600">Hari Tidak Hadir</div>
                                        </div>
                                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                            <div class="text-2xl font-semibold text-yellow-700">1</div>
                                            <div class="text-sm text-yellow-600">Terlambat</div>
                                        </div>
                                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                                            <div class="text-2xl font-semibold text-blue-700">90%</div>
                                            <div class="text-sm text-blue-600">Persentase</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Riwayat Kehadiran -->
                            <div class="card">
                                <div class="p-6 pb-4">
                                    <h3 class="font-semibold flex items-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Kehadiran Terbaru
                                    </h3>
                                </div>
                                <div class="px-6 pb-6">
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-medium">13 Ags 2025</div>
                                                <div class="text-sm text-gray-500">Masuk: 07:15 | Pulang: 15:30</div>
                                            </div>
                                            <span class="badge-success px-2 py-1 rounded text-xs">Hadir</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-medium">12 Ags 2025</div>
                                                <div class="text-sm text-gray-500">Masuk: 07:20 | Pulang: 15:30</div>
                                            </div>
                                            <span class="badge-success px-2 py-1 rounded text-xs">Hadir</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-medium">11 Ags 2025</div>
                                                <div class="text-sm text-gray-500">Masuk: - | Pulang: -</div>
                                            </div>
                                            <span class="badge-secondary px-2 py-1 rounded text-xs">Sakit</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-medium">10 Ags 2025</div>
                                                <div class="text-sm text-gray-500">Masuk: 07:10 | Pulang: 15:30</div>
                                            </div>
                                            <span class="badge-success px-2 py-1 rounded text-xs">Hadir</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <div class="font-medium">9 Ags 2025</div>
                                                <div class="text-sm text-gray-500">Masuk: 07:25 | Pulang: 15:30</div>
                                            </div>
                                            <span class="badge-warning px-2 py-1 rounded text-xs">Terlambat</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Sections -->
                <div id="nilai" class="content-section">
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-2">Halaman dalam pengembangan</h3>
                            <p class="text-gray-500">Fitur Rapor & Nilai akan segera tersedia</p>
                        </div>
                    </div>
                </div>

                <div id="profil" class="content-section">
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-2">Halaman dalam pengembangan</h3>
                            <p class="text-gray-500">Fitur Profil Siswa akan segera tersedia</p>
                        </div>
                    </div>
                </div>

                <div id="pengaturan" class="content-section">
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-2">Halaman dalam pengembangan</h3>
                            <p class="text-gray-500">Fitur Pengaturan akan segera tersedia</p>
                        </div>
                    </div>
                </div>

                <div id="bantuan" class="content-section">
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-2">Halaman dalam pengembangan</h3>
                            <p class="text-gray-500">Fitur Bantuan akan segera tersedia</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.classList.remove('active');
            });

            // Remove active class from all menu buttons
            const menuBtns = document.querySelectorAll('.menu-btn');
            menuBtns.forEach(btn => {
                btn.classList.remove('active-menu');
            });

            // Show selected section
            document.getElementById(sectionId).classList.add('active');

            // Add active class to clicked menu button
            event.target.classList.add('active-menu');
        }
    </script>
</body>
</html>
