@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-12">

    {{-- Card Utama --}}
    <div class="card mb-4">
      <h5 class="card-header">{{ $title }}</h5>
      <div class="card-body">

        {{-- Info Siswa --}}
        <div class="row mb-4 align-items-start">

          {{-- Foto Siswa --}}
          <div class="col-md-2 mb-3 d-flex flex-column">
            <h6 class="fw-bold mb-3">Foto Siswa</h6>
            @php
                $fotoPath = $siswa->foto && Storage::exists($siswa->foto)
                    ? Storage::url($siswa->foto)
                    : asset('images/no-image.png');
            @endphp
            <div style="width: 85%; aspect-ratio: 3/4; overflow: hidden;">
              <img src="{{ $fotoPath }}" alt="Foto Siswa" class="rounded shadow-sm border" style="width: 100%; height: 100%; object-fit: cover;">
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
                <th class="text-center" style="width: 120px;">Aksi</th> {{-- Kolom hapus --}}
              </tr>
            </thead>
            <tbody>
              @php $totalTagihan = 0; @endphp
              @forelse ($tagihan as $item)
                  @foreach($item->details as $detail)
                      @php $totalTagihan += $detail->jumlah_biaya; @endphp
                      <tr>
                        <td class="text-center">{{ $loop->parent->iteration }}.{{ $loop->iteration }}</td>
                        <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                        <td>{{ $detail->nama_biaya }}</td>
                        <td class="text-end">{{ 'Rp ' . number_format($detail->jumlah_biaya, 0, ',', '.') }}</td>
                        <td class="text-center">
                          @if ($item->status === 'baru')
                            <span class="badge bg-warning"><i class="fa fa-clock me-1"></i> Baru</span>
                          @elseif ($item->status === 'lunas')
                            <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Lunas</span>
                          @else
                            <span class="badge bg-secondary"><i class="fa fa-info-circle me-1"></i> {{ ucfirst($item->status) }}</span>
                          @endif
                        </td>
                        <td class="text-center">
                          <form action="{{ route('tagihan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tagihan ini beserta semua detailnya?')">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                  <i class="fa fa-trash me-1"></i> Hapus
                              </button>
                          </form>
                        </td>
                      </tr>
                  @endforeach
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted"><i class="fa fa-info-circle me-1"></i>Tidak ada tagihan untuk siswa ini.</td>
                </tr>
              @endforelse
            </tbody>

            @if ($totalTagihan > 0)
              <tfoot class="table-secondary">
                <tr>
                  <th colspan="3" class="text-end">Total</th>
                  <th class="text-end">{{ 'Rp ' . number_format($totalTagihan, 0, ',', '.') }}</th>
                  <th colspan="2"></th>
                </tr>
              </tfoot>
            @endif
          </table>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-4">
          <a href="{{ route('tagihan.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left me-1"></i> Kembali</a>
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
