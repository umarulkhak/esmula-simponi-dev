@extends('layouts.app_sneat')

@section('content')

<head>
    <meta charset="UTF-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans">
    <!-- Main Content -->
    <main>
        <div class="max-w-7xl mx-auto">
            
            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content">
                <div class="space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                                    <p class="text-2xl font-bold text-gray-900">245</p>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100">
                                    <i class="fas fa-users text-blue-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pembayaran Bulan Ini</p>
                                    <p class="text-xl font-bold text-gray-900"> Rp 21.300.000 </p>
                                </div>
                                <div class="p-3 rounded-full bg-green-100">
                                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Tagihan Tertunggak</p>
                                    <p class="text-2xl font-bold text-gray-900">32</p>
                                </div>
                                <div class="p-3 rounded-full bg-red-100">
                                    <i class="fas fa-exclamation-circle text-red-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Lunas Bulan Ini</p>
                                    <p class="text-2xl font-bold text-gray-900">213</p>
                                </div>
                                <div class="p-3 rounded-full bg-emerald-100">
                                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Payments & Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Pembayaran Terbaru</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">Ahmad Fadil</p>
                                            <p class="text-sm text-gray-500">IX-A</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-green-600">Rp 100.000</p>
                                            <p class="text-sm text-gray-500">2025-01-20</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">Siti Aminah</p>
                                            <p class="text-sm text-gray-500">VIII-B</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-green-600">Rp 100.000</p>
                                            <p class="text-sm text-gray-500">2025-01-19</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">Muhammad Rizki</p>
                                            <p class="text-sm text-gray-500">VII-C</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-green-600">Rp 100.000</p>
                                            <p class="text-sm text-gray-500">2025-01-18</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">Fatimah Zahra</p>
                                            <p class="text-sm text-gray-500">IX-B</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium text-green-600">Rp 100.000</p>
                                            <p class="text-sm text-gray-500">2025-01-17</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b border-gray-200">
                                <h3 class="text-lg font-semibold">Ringkasan Tagihan</h3>
                            </div>
                            <div class="p-6">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-600">SPP Januari 2025</span>
                                            <span class="font-semibold text-green-600">87% Terkumpul</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 87%"></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-600">SPP Desember 2024</span>
                                            <span class="font-semibold text-green-600">95% Terkumpul</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 95%"></div>
                                        </div>
                                    </div>

                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-gray-600">SPP November 2024</span>
                                            <span class="font-semibold text-green-600">100% Terkumpul</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Tab -->
            <div id="students-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="text-lg font-semibold">Data Siswa</h3>
                            <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                Tambah Siswa
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Cari siswa berdasarkan nama, NIS, atau kelas..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-3 px-4">NIS</th>
                                        <th class="text-left py-3 px-4">Nama Siswa</th>
                                        <th class="text-left py-3 px-4">Kelas</th>
                                        <th class="text-left py-3 px-4">Alamat</th>
                                        <th class="text-left py-3 px-4">Nama Orang Tua</th>
                                        <th class="text-left py-3 px-4">Status</th>
                                        <th class="text-left py-3 px-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12345</td>
                                        <td class="py-3 px-4">Ahmad Fadil</td>
                                        <td class="py-3 px-4">IX-A</td>
                                        <td class="py-3 px-4">Jl. Merdeka No. 123</td>
                                        <td class="py-3 px-4">Budi Santoso</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12346</td>
                                        <td class="py-3 px-4">Siti Aminah</td>
                                        <td class="py-3 px-4">VIII-B</td>
                                        <td class="py-3 px-4">Jl. Sudirman No. 45</td>
                                        <td class="py-3 px-4">Ahmad Wijaya</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12347</td>
                                        <td class="py-3 px-4">Muhammad Rizki</td>
                                        <td class="py-3 px-4">VII-C</td>
                                        <td class="py-3 px-4">Jl. Diponegoro No. 67</td>
                                        <td class="py-3 px-4">Slamet Riyadi</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12348</td>
                                        <td class="py-3 px-4">Fatimah Zahra</td>
                                        <td class="py-3 px-4">IX-B</td>
                                        <td class="py-3 px-4">Jl. Kartini No. 89</td>
                                        <td class="py-3 px-4">Hasan Basri</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12349</td>
                                        <td class="py-3 px-4">Ali Rahman</td>
                                        <td class="py-3 px-4">VIII-A</td>
                                        <td class="py-3 px-4">Jl. Pahlawan No. 12</td>
                                        <td class="py-3 px-4">Umar Bakri</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12350</td>
                                        <td class="py-3 px-4">Khadijah Putri</td>
                                        <td class="py-3 px-4">VII-A</td>
                                        <td class="py-3 px-4">Jl. Veteran No. 34</td>
                                        <td class="py-3 px-4">Yusuf Hakim</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-gray-500 text-white px-2 py-1 rounded-full text-xs">Tidak Aktif</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex space-x-2">
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="text-gray-600 hover:text-gray-900">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="text-red-600 hover:text-red-700">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments Tab -->
            <div id="payments-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <h3 class="text-lg font-semibold">Data Pembayaran SPP</h3>
                            <div class="flex gap-2">
                                <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-50">
                                    <i class="fas fa-download"></i>
                                    Export
                                </button>
                                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-plus"></i>
                                    Input Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-4 mb-6">
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Cari berdasarkan nama, NIS, atau kelas..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>
                            <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option>Semua Status</option>
                                <option>Lunas</option>
                                <option>Belum Bayar</option>
                                <option>Tertunggak</option>
                            </select>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-3 px-4">NIS</th>
                                        <th class="text-left py-3 px-4">Nama Siswa</th>
                                        <th class="text-left py-3 px-4">Kelas</th>
                                        <th class="text-left py-3 px-4">Bulan</th>
                                        <th class="text-left py-3 px-4">Jumlah</th>
                                        <th class="text-left py-3 px-4">Tanggal Bayar</th>
                                        <th class="text-left py-3 px-4">Metode</th>
                                        <th class="text-left py-3 px-4">Status</th>
                                        <th class="text-left py-3 px-4">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12345</td>
                                        <td class="py-3 px-4">Ahmad Fadil</td>
                                        <td class="py-3 px-4">IX-A</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">2025-01-20</td>
                                        <td class="py-3 px-4">Transfer</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Lunas</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                Cetak Kwitansi
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12346</td>
                                        <td class="py-3 px-4">Siti Aminah</td>
                                        <td class="py-3 px-4">VIII-B</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">2025-01-19</td>
                                        <td class="py-3 px-4">Tunai</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Lunas</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                Cetak Kwitansi
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12347</td>
                                        <td class="py-3 px-4">Muhammad Rizki</td>
                                        <td class="py-3 px-4">VII-C</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">-</td>
                                        <td class="py-3 px-4">-</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-gray-500 text-white px-2 py-1 rounded-full text-xs">Belum Bayar</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                Bayar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12348</td>
                                        <td class="py-3 px-4">Fatimah Zahra</td>
                                        <td class="py-3 px-4">IX-B</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">2025-01-17</td>
                                        <td class="py-3 px-4">Transfer</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Lunas</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                Cetak Kwitansi
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12349</td>
                                        <td class="py-3 px-4">Ali Rahman</td>
                                        <td class="py-3 px-4">VIII-A</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">-</td>
                                        <td class="py-3 px-4">-</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-red-600 text-white px-2 py-1 rounded-full text-xs">Tertunggak</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs">
                                                Bayar
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium">12350</td>
                                        <td class="py-3 px-4">Khadijah Putri</td>
                                        <td class="py-3 px-4">VII-A</td>
                                        <td class="py-3 px-4">Januari 2025</td>
                                        <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                        <td class="py-3 px-4">2025-01-15</td>
                                        <td class="py-3 px-4">Tunai</td>
                                        <td class="py-3 px-4">
                                            <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Lunas</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                Cetak Kwitansi
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bills Tab -->
            <div id="bills-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Summary Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Ringkasan Tagihan</h3>
                                <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    <option>Januari 2025</option>
                                    <option>Desember 2024</option>
                                    <option>November 2024</option>
                                </select>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                <div class="bg-blue-50 p-4 rounded-lg">
                                    <p class="text-sm text-blue-600 font-medium">Total Siswa</p>
                                    <p class="text-2xl font-bold text-blue-700">245</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg">
                                    <p class="text-sm text-green-600 font-medium">Total Tagihan</p>
                                    <p class="text-2xl font-bold text-green-700">Rp 122.500.000</p>
                                </div>
                                <div class="bg-emerald-50 p-4 rounded-lg">
                                    <p class="text-sm text-emerald-600 font-medium">Sudah Bayar</p>
                                    <p class="text-2xl font-bold text-emerald-700">213</p>
                                </div>
                                <div class="bg-red-50 p-4 rounded-lg">
                                    <p class="text-sm text-red-600 font-medium">Belum Bayar</p>
                                    <p class="text-2xl font-bold text-red-700">32</p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700">Progress Pembayaran</span>
                                    <span class="text-sm font-bold text-green-600">87%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-green-600 h-3 rounded-full transition-all duration-300" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bills Management -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Manajemen Tagihan</h3>
                                <div class="flex gap-2">
                                    <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-50">
                                        <i class="fas fa-paper-plane"></i>
                                        Kirim Notifikasi
                                    </button>
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                        <i class="fas fa-plus"></i>
                                        Buat Tagihan Baru
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3 px-4">Bulan/Tahun</th>
                                            <th class="text-left py-3 px-4">Kelas</th>
                                            <th class="text-left py-3 px-4">Jumlah Siswa</th>
                                            <th class="text-left py-3 px-4">Nominal/Siswa</th>
                                            <th class="text-left py-3 px-4">Total Tagihan</th>
                                            <th class="text-left py-3 px-4">Sudah Bayar</th>
                                            <th class="text-left py-3 px-4">Belum Bayar</th>
                                            <th class="text-left py-3 px-4">Status</th>
                                            <th class="text-left py-3 px-4">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Januari 2025</td>
                                            <td class="py-3 px-4">VII</td>
                                            <td class="py-3 px-4">82</td>
                                            <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                            <td class="py-3 px-4 font-semibold">Rp 41.000.000</td>
                                            <td class="py-3 px-4 text-green-600">70</td>
                                            <td class="py-3 px-4 text-red-600">12</td>
                                            <td class="py-3 px-4">
                                                <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex space-x-2">
                                                    <button class="border border-gray-300 text-gray-700 px-3 py-1 rounded text-xs hover:bg-gray-50">
                                                        Edit
                                                    </button>
                                                    <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                        Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Januari 2025</td>
                                            <td class="py-3 px-4">VIII</td>
                                            <td class="py-3 px-4">78</td>
                                            <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                            <td class="py-3 px-4 font-semibold">Rp 39.000.000</td>
                                            <td class="py-3 px-4 text-green-600">68</td>
                                            <td class="py-3 px-4 text-red-600">10</td>
                                            <td class="py-3 px-4">
                                                <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex space-x-2">
                                                    <button class="border border-gray-300 text-gray-700 px-3 py-1 rounded text-xs hover:bg-gray-50">
                                                        Edit
                                                    </button>
                                                    <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                        Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Januari 2025</td>
                                            <td class="py-3 px-4">IX</td>
                                            <td class="py-3 px-4">85</td>
                                            <td class="py-3 px-4 font-semibold text-green-600">Rp 500.000</td>
                                            <td class="py-3 px-4 font-semibold">Rp 42.500.000</td>
                                            <td class="py-3 px-4 text-green-600">75</td>
                                            <td class="py-3 px-4 text-red-600">10</td>
                                            <td class="py-3 px-4">
                                                <span class="bg-green-600 text-white px-2 py-1 rounded-full text-xs">Aktif</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex space-x-2">
                                                    <button class="border border-gray-300 text-gray-700 px-3 py-1 rounded text-xs hover:bg-gray-50">
                                                        Edit
                                                    </button>
                                                    <button class="border border-blue-300 text-blue-600 px-3 py-1 rounded text-xs hover:bg-blue-50">
                                                        Detail
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Tab -->
            <div id="reports-tab" class="tab-content hidden">
                <div class="space-y-6">
                    <!-- Header -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Laporan Pembayaran SPP</h3>
                                <div class="flex gap-2">
                                    <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                        <option>Januari 2025</option>
                                        <option>Desember 2024</option>
                                        <option>November 2024</option>
                                        <option>Oktober 2024</option>
                                    </select>
                                    <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-50">
                                        <i class="fas fa-download"></i>
                                        Export PDF
                                    </button>
                                    <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 hover:bg-gray-50">
                                        <i class="fas fa-download"></i>
                                        Export Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Monthly Trend -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                <i class="fas fa-chart-line text-green-600"></i>
                                Tren Pembayaran Bulanan
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3 px-4">Bulan</th>
                                            <th class="text-left py-3 px-4">Target</th>
                                            <th class="text-left py-3 px-4">Realisasi</th>
                                            <th class="text-left py-3 px-4">Persentase</th>
                                            <th class="text-left py-3 px-4">Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Januari 2025</td>
                                            <td class="py-3 px-4">Rp 122.500.000</td>
                                            <td class="py-3 px-4 text-green-600 font-semibold">Rp 106.500.000</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">87%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 87%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Desember 2024</td>
                                            <td class="py-3 px-4">Rp 122.500.000</td>
                                            <td class="py-3 px-4 text-green-600 font-semibold">Rp 116.375.000</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-green-600">95%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 95%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">November 2024</td>
                                            <td class="py-3 px-4">Rp 122.500.000</td>
                                            <td class="py-3 px-4 text-green-600 font-semibold">Rp 122.500.000</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-green-600">100%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">Oktober 2024</td>
                                            <td class="py-3 px-4">Rp 122.500.000</td>
                                            <td class="py-3 px-4 text-green-600 font-semibold">Rp 120.225.000</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-green-600">98%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 98%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Class Performance -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-semibold flex items-center gap-2">
                                <i class="fas fa-chart-pie text-green-600"></i>
                                Laporan Per Kelas - Januari 2025
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3 px-4">Kelas</th>
                                            <th class="text-left py-3 px-4">Total Siswa</th>
                                            <th class="text-left py-3 px-4">Sudah Lunas</th>
                                            <th class="text-left py-3 px-4">Belum Bayar</th>
                                            <th class="text-left py-3 px-4">Persentase</th>
                                            <th class="text-left py-3 px-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VII-A</td>
                                            <td class="py-3 px-4">28</td>
                                            <td class="py-3 px-4 text-green-600">25</td>
                                            <td class="py-3 px-4 text-red-600">3</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">89%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 89%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VII-B</td>
                                            <td class="py-3 px-4">27</td>
                                            <td class="py-3 px-4 text-green-600">24</td>
                                            <td class="py-3 px-4 text-red-600">3</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">89%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 89%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VII-C</td>
                                            <td class="py-3 px-4">27</td>
                                            <td class="py-3 px-4 text-green-600">21</td>
                                            <td class="py-3 px-4 text-red-600">6</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-red-600">78%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-red-500 h-2 rounded-full" style="width: 78%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VIII-A</td>
                                            <td class="py-3 px-4">26</td>
                                            <td class="py-3 px-4 text-green-600">23</td>
                                            <td class="py-3 px-4 text-red-600">3</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">88%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 88%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VIII-B</td>
                                            <td class="py-3 px-4">26</td>
                                            <td class="py-3 px-4 text-green-600">24</td>
                                            <td class="py-3 px-4 text-red-600">2</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-green-600">92%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 92%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">VIII-C</td>
                                            <td class="py-3 px-4">26</td>
                                            <td class="py-3 px-4 text-green-600">21</td>
                                            <td class="py-3 px-4 text-red-600">5</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">81%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 81%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">IX-A</td>
                                            <td class="py-3 px-4">28</td>
                                            <td class="py-3 px-4 text-green-600">26</td>
                                            <td class="py-3 px-4 text-red-600">2</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-green-600">93%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-green-600 h-2 rounded-full" style="width: 93%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">IX-B</td>
                                            <td class="py-3 px-4">29</td>
                                            <td class="py-3 px-4 text-green-600">25</td>
                                            <td class="py-3 px-4 text-red-600">4</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">86%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 86%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium">IX-C</td>
                                            <td class="py-3 px-4">28</td>
                                            <td class="py-3 px-4 text-green-600">24</td>
                                            <td class="py-3 px-4 text-red-600">4</td>
                                            <td class="py-3 px-4">
                                                <span class="font-semibold text-yellow-600">86%</span>
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 86%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
        </div>

@endsection
