@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">

    {{-- Card Utama --}}
    <div class="card mb-4">
      <h5 class="card-header">
        <i class="fa fa-file-invoice me-1"></i> {{ $title }}
      </h5>
      <div class="card-body">

        {{-- Info Siswa --}}
        <div class="row mb-4">
          <div class="col-md-6">
            <h6 class="fw-bold mb-3 text-primary">
              <i class="fa fa-user-graduate me-1"></i> Informasi Siswa
            </h6>
            <div class="table-responsive">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th style="width: 130px;">Nama</th>
                    <td>{{ $siswa->nama }}</td>
                  </tr>
                  <tr>
                    <th>NISN</th>
                    <td>{{ $siswa->nisn }}</td>
                  </tr>
                  <tr>
                    <th>Kelas</th>
                    <td>{{ $siswa->kelas }}</td>
                  </tr>
                  <tr>
                    <th>Angkatan</th>
                    <td>{{ $siswa->angkatan }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Daftar Tagihan --}}
        <h6 class="fw-bold mb-3 text-primary">
          <i class="fa fa-file-invoice-dollar me-1"></i> Daftar Tagihan
        </h6>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-secondary">
              <tr>
                <th class="text-center" style="width: 60px;">No</th>
                <th>Tanggal Tagihan</th>
                <th>Nama Biaya</th>
                <th class="text-end">Jumlah</th>
                <th class="text-center">Status</th>
              </tr>
            </thead>
            <tbody>
              @php $totalTagihan = 0; @endphp
              @forelse ($tagihan as $item)
                @php $totalTagihan += $item->jumlah_biaya; @endphp
                <tr>
                  <td class="text-center">{{ $loop->iteration }}</td>
                  <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                  <td>{{ $item->nama_biaya }}</td>
                  <td class="text-end">{{ $item->formatRupiah('jumlah_biaya') }}</td>
                  <td class="text-center">
                    @if ($item->status === 'baru')
                      <span class="badge bg-warning">
                        <i class="fa fa-clock me-1"></i> Baru
                      </span>
                    @elseif ($item->status === 'lunas')
                      <span class="badge bg-success">
                        <i class="fa fa-check-circle me-1"></i> Lunas
                      </span>
                    @else
                      <span class="badge bg-secondary">
                        <i class="fa fa-info-circle me-1"></i> {{ ucfirst($item->status) }}
                      </span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted">
                    <i class="fa fa-info-circle me-1"></i>
                    Tidak ada tagihan untuk siswa ini.
                  </td>
                </tr>
              @endforelse
            </tbody>

            @if ($tagihan->count())
              <tfoot class="table-secondary">
                <tr>
                  <th colspan="3" class="text-end">Total</th>
                  <th class="text-end">
                    {{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}
                  </th>
                  <th></th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-4">
          <a href="{{ route('tagihan.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Kembali
          </a>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
