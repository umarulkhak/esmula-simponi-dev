@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">

    {{-- Card Utama --}}
    <div class="card mb-4">
      <h5 class="card-header">{{ $title }}</h5>
      <div class="card-body">

        {{-- Flash Message --}}
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @elseif(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        {{-- Info Siswa --}}
        <div class="row mb-4 align-items-start">
          {{-- Foto Siswa --}}
          <div class="col-md-2 mb-3 d-flex flex-column">
            <h6 class="fw-bold mb-3">Foto Siswa</h6>
            @php
                $fotoPath = ($siswa->foto && Storage::exists($siswa->foto))
                    ? Storage::url($siswa->foto)
                    : asset('images/no-image.png');
            @endphp
            <div style="width: 85%; aspect-ratio: 3/4; overflow: hidden; border-radius: 0.5rem;">
              <img src="{{ $fotoPath }}"
                   alt="Foto {{ $siswa->nama ?? 'Siswa' }}"
                   class="rounded shadow-sm border"
                   style="width: 100%; height: 100%; object-fit: cover; object-position: center;">
            </div>
          </div>

          {{-- Detail Siswa --}}
          <div class="col-md-4">
            <h6 class="fw-bold mb-3">Informasi Siswa</h6>
            <div class="table-responsive">
              <table class="table table-bordered mb-0">
                <tbody>
                  <tr>
                    <th style="width: 130px;">Nama</th>
                    <td>{{ $siswa->nama ?? '—' }}</td>
                  </tr>
                  <tr>
                    <th>NISN</th>
                    <td>{{ $siswa->nisn ?? '—' }}</td>
                  </tr>
                  <tr>
                    <th>Kelas</th>
                    <td>{{ $siswa->kelas ?? '—' }}</td>
                  </tr>
                  <tr>
                    <th>Angkatan</th>
                    <td>{{ $siswa->angkatan ?? '—' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Filter Bulan/Tahun --}}
        <div class="mb-4">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label for="filter_bulan" class="form-label mb-0">Bulan</label>
                    <select name="bulan" id="filter_bulan" class="form-select form-select-sm">
                        <option value="">Semua Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}"
                                {{ request('bulan') == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <label for="filter_tahun" class="form-label mb-0">Tahun</label>
                    <select name="tahun" id="filter_tahun" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @for ($y = date('Y'); $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary btn-sm">
                        <i class="fa fa-filter me-1"></i> Filter
                    </button>
                    @if(request()->filled(['bulan', 'tahun']))
                        <a href="{{ route('tagihan.show', $siswa->id) }}" class="btn btn-outline-secondary btn-sm ms-2">
                            <i class="fa fa-undo me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Daftar Tagihan --}}
        <h6 class="fw-bold mb-3">Daftar Tagihan</h6>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-secondary">
              <tr>
                <th class="text-center" style="width: 60px;">No</th>
                <th>Tanggal Tagihan</th>
                <th>Nama Biaya</th>
                <th class="text-end">Jumlah</th>
                <th class="text-center">Status</th>
                <th class="text-center" style="width: 80px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $totalTagihan = 0; @endphp
              @forelse ($tagihan as $item)
                  @if($item->tagihanDetails && $item->tagihanDetails->isNotEmpty())
                      @foreach($item->tagihanDetails as $detail)
                          @php $totalTagihan += $detail->jumlah_biaya; @endphp
                          <tr>
                            <td class="text-center">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                            <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                            <td>{{ $detail->nama_biaya ?? '—' }}</td>
                            <td class="text-end">{{ 'Rp ' . number_format($detail->jumlah_biaya, 0, ',', '.') }}</td>
                            <td class="text-center">
                              @if ($item->status === 'baru')
                                <span class="badge bg-warning"><i class="fa fa-clock me-1"></i> Baru</span>
                              @elseif ($item->status === 'lunas')
                                <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Lunas</span>
                              @else
                                <span class="badge bg-secondary"><i class="fa fa-info-circle me-1"></i> {{ ucfirst($item->status ?? 'tidak diketahui') }}</span>
                              @endif
                            </td>
                            <td class="text-center">
                              <form action="{{ route('tagihan.destroy', $item->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus tagihan ini beserta semua detailnya? Tindakan tidak bisa dibatalkan.')">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center" title="Hapus tagihan ini">
                                      <i class="fa fa-trash"></i>
                                  </button>
                              </form>
                            </td>
                          </tr>
                      @endforeach
                  @endif
              @empty
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fa fa-receipt fa-2x text-muted mb-2"></i>
                        <span class="text-muted">
                            @if(request()->filled(['bulan', 'tahun']))
                                Tidak ada tagihan untuk periode
                                <strong>{{ \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') }} {{ request('tahun') }}</strong>.
                            @else
                                Siswa ini belum memiliki tagihan.
                            @endif
                        </span>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
            @if ($totalTagihan > 0)
              <tfoot class="table-secondary">
                <tr>
                  <th colspan="3" class="text-end">Total Tagihan</th>
                  <th class="text-end fw-bold">{{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}</th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-4">
          <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
          </a>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection

{{--
    Dibuat oleh: Umar Ulkhak
    Diperbarui: 6 September 2025
    Fitur:
    - Menampilkan detail tagihan per siswa
    - Filter bulan/tahun
    - Total tagihan di footer
    - Tombol hapus seragam dengan index (outline + ikon)
    - UX pesan kosong yang informatif
--}}
