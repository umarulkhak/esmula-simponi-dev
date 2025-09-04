@extends('layouts.app_sneat')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#696cff',
                        secondary: '#8592a3',
                        muted: '#ececf0',
                        'muted-foreground': '#717182',
                        border: 'rgba(0, 0, 0, 0.1)',
                        background: '#ffffff',
                        foreground: '#030213',
                        card: '#ffffff',
                        'card-foreground': '#030213'
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-background">
    <div class="h-screen flex flex-col">

        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar -->

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-7xl mx-auto space-y-6">
                    <!-- Page Header -->
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-semibold">Beranda</h1>
                            <p class="text-gray-600">
                                Selamat datang di Simponi SMP Muhammadiyah Larangan
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Export Laporan
                            </button>
                            <button class="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90">
                                <i data-lucide="calendar" class="w-4 h-4"></i>
                                Buat Tagihan Baru
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Siswa</p>
                                    <p class="text-2xl font-semibold">176</p>
                                    <p class="text-sm text-green-600">+5 siswa baru</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Pembayaran Bulan Ini</p>
                                    <p class="text-2xl font-semibold">Rp 58.450.000</p>
                                    <p class="text-sm text-green-600">+12% dari bulan lalu</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center">
                                    <i data-lucide="credit-card" class="w-6 h-6 text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Tingkat Pembayaran</p>
                                    <p class="text-2xl font-semibold">92%</p>
                                    <p class="text-sm text-green-600">+3% dari bulan lalu</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-primary"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Tunggakan</p>
                                    <p class="text-2xl font-semibold">14</p>
                                    <p class="text-sm text-red-600">6 tagihan belum dibayar</p>
                                </div>
                                <div class="w-12 h-12 rounded-lg bg-red-100 flex items-center justify-center">
                                    <i data-lucide="alert-circle" class="w-6 h-6 text-red-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Overview -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Payment Chart -->
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Statistik Pembayaran</h3>
                            </div>
                            <div class="p-6">
                                <canvas id="paymentChart" class="w-full h-80"></canvas>
                            </div>
                        </div>

                        <!-- Student Overview -->
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-semibold">Ringkasan Per Kelas</h3>
                                <button class="flex items-center gap-2 px-3 py-1 bg-primary text-white text-sm rounded-lg">
                                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                                    Tambah Siswa
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">VII</p>
                                            <p class="text-sm text-gray-600">32 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">28 Lunas</span>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">2 Pending</span>
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">2 Belum</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">VIII</p>
                                            <p class="text-sm text-gray-600">30 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">25 Lunas</span>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">3 Pending</span>
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">2 Belum</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">IX</p>
                                            <p class="text-sm text-gray-600">31 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">30 Lunas</span>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">1 Pending</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">XI IPA 2</p>
                                            <p class="text-sm text-gray-600">29 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">27 Lunas</span>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">1 Pending</span>
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">1 Belum</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">XII IPA 1</p>
                                            <p class="text-sm text-gray-600">28 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">28 Lunas</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div>
                                            <p class="font-medium">XII IPS 1</p>
                                            <p class="text-sm text-gray-600">26 siswa</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">24 Lunas</span>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">1 Pending</span>
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">1 Belum</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p class="text-2xl font-semibold text-green-600">162</p>
                                            <p class="text-xs text-gray-600">Total Lunas</p>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-semibold text-yellow-600">8</p>
                                            <p class="text-xs text-gray-600">Pending</p>
                                        </div>
                                        <div>
                                            <p class="text-2xl font-semibold text-red-600">6</p>
                                            <p class="text-xs text-gray-600">Belum Bayar</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Recent Payments -->
                        <div class="lg:col-span-2 bg-white rounded-lg border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Pembayaran Terbaru</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium">Ahmad Rizki</p>
                                            <p class="text-sm text-gray-600">XII IPA 1 • Januari 2024</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="font-semibold">Rp 350.000</p>
                                                <p class="text-xs text-gray-600">2024-01-15</p>
                                            </div>
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Lunas</span>
                                            <button class="p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium">Siti Nurhaliza</p>
                                            <p class="text-sm text-gray-600">XI IPS 2 • Januari 2024</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="font-semibold">Rp 350.000</p>
                                                <p class="text-xs text-gray-600">2024-01-14</p>
                                            </div>
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Lunas</span>
                                            <button class="p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium">Budi Santoso</p>
                                            <p class="text-sm text-gray-600">X MIPA 1 • Januari 2024</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="font-semibold">Rp 300.000</p>
                                                <p class="text-xs text-gray-600">2024-01-13</p>
                                            </div>
                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded">Pending</span>
                                            <button class="p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium">Maya Sari</p>
                                            <p class="text-sm text-gray-600">XII IPS 1 • Januari 2024</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="font-semibold">Rp 350.000</p>
                                                <p class="text-xs text-gray-600">2024-01-12</p>
                                            </div>
                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded">Lunas</span>
                                            <button class="p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                        <div class="flex-1">
                                            <p class="font-medium">Andi Kurniawan</p>
                                            <p class="text-sm text-gray-600">XI IPA 2 • Januari 2024</p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="text-right">
                                                <p class="font-semibold">Rp 350.000</p>
                                                <p class="text-xs text-gray-600">-</p>
                                            </div>
                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded">Belum Bayar</span>
                                            <button class="p-2 border border-gray-200 rounded hover:bg-gray-50">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <button class="w-full py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        Lihat Semua Pembayaran
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Log -->
                        <div class="bg-white rounded-lg border border-gray-200">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Aktivitas Terbaru</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium">Pembayaran diterima</p>
                                            <p class="text-xs text-gray-600">Ahmad Rizki - XII IPA 1</p>
                                            <p class="text-xs text-gray-600">5 menit yang lalu</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium">Siswa baru terdaftar</p>
                                            <p class="text-xs text-gray-600">Maya Putri - X MIPA 2</p>
                                            <p class="text-xs text-gray-600">2 jam yang lalu</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium">Reminder dikirim</p>
                                            <p class="text-xs text-gray-600">5 siswa dengan tunggakan</p>
                                            <p class="text-xs text-gray-600">1 hari yang lalu</p>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium">Tagihan jatuh tempo</p>
                                            <p class="text-xs text-gray-600">SPP Januari 2024</p>
                                            <p class="text-xs text-gray-600">3 hari yang lalu</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <button class="w-full py-2 border border-gray-200 rounded-lg hover:bg-gray-50">
                                        Lihat Semua Aktivitas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Initialize Chart
        const ctx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                datasets: [{
                    label: 'Lunas (%)',
                    data: [85, 90, 88, 92, 95, 87],
                    backgroundColor: '#696cff',
                    borderRadius: 4,
                }, {
                    label: 'Belum Bayar (%)',
                    data: [15, 10, 12, 8, 5, 13],
                    backgroundColor: '#e9ecef',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Simple search functionality
        const searchInput = document.querySelector('input[type="search"]');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                console.log('Searching for:', e.target.value);
                // Implement search logic here
            });
        }

        // Notification click handler
        const notificationBtn = document.querySelector('button[class*="relative"]');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                alert('3 notifikasi baru:\n- Pembayaran Ahmad Rizki diterima\n- Reminder tunggakan dikirim\n- 2 tagihan akan jatuh tempo');
            });
        }
    </script>
</body>
</html>
                </div>
            </div>
        </div>
</div>
@endsection
