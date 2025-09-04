{{-- resources/views/operator/tagihan_index.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">{{ $title }}</h5>
            <div class="card-body">

                {{-- Tombol Tambah Data (kiri) + Form Pencarian (kanan) --}}
                <div class="row mb-3 align-items-center">
                    {{-- Tombol Tambah Data --}}
                    <div class="col-md-4 mb-2 mb-md-0">
                        <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm mb-3">
                            <i class="fa fa-plus"></i>
                            <span>Tambah Data</span>
                        </a>
                    </div>

                    {{-- Form Pencarian --}}
                    <div class="col-md-8 text-md-end">
                        {!! Form::open(['route' => $routePrefix . '.index', 'method' => 'GET']) !!}
                        <div class="row g-2 justify-content-md-end">

                            {{-- Bulan --}}
                            <div class="col-12 col-md-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar-alt"></i>
                                    </span>
                                    {!! Form::selectMonth('bulan', request('bulan'), [
                                        'class' => 'form-control',
                                        'id' => 'filter_bulan',
                                        'placeholder' => 'Pilih Bulan'
                                    ]) !!}
                                </div>
                            </div>

                            {{-- Tahun --}}
                            <div class="col-12 col-md-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    {!! Form::selectRange('tahun', 2022, date('Y') + 1, request('tahun'), [
                                        'class' => 'form-control',
                                        'id' => 'filter_tahun',
                                        'placeholder' => 'Pilih Tahun'
                                    ]) !!}
                                </div>
                            </div>

                            {{-- Tombol Tampil --}}
                            <div class="col-12 col-md-auto">
                                <button class="btn btn-secondary w-100 w-md-auto" type="submit" id="btn_filter">
                                    <i class="fa fa-search"></i>
                                    <span>Tampil</span>
                                </button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                {{-- Tabel Data --}}
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>Tanggal Tagihan</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 220px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->siswa->nisn }}</td>
                                    <td>{{ $item->siswa->nama }}</td>
                                    <td>{{ $item->siswa->kelas }}</td>
                                    <td>{{ $item->tanggal_tagihan->translatedFormat('d M Y') }}</td>
                                    <td>
                                        @if($item->status == 'baru')
                                            <span class="badge bg-warning">Baru</span>
                                        @elseif($item->status == 'lunas')
                                            <span class="badge bg-success">Lunas</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($item->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Edit --}}
                                            {{-- <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm d-flex align-items-center gap-1">
                                                <i class="fa fa-edit"></i>
                                                <span>Edit</span>
                                            </a> --}}

                                            {{-- Detail --}}
                                            <a href="{{ route($routePrefix . '.show', [
                                                $item->siswa,
                                                'siswa_id' => $item->siswa_id,
                                                'bulan' => $item->tanggal_tagihan->format('m'),
                                                'tahun' => $item->tanggal_tagihan->format('y'),
                                                ]) }}"
                                               class="btn btn-info btn-sm d-flex align-items-center gap-1">
                                                <i class="fa fa-info"></i>
                                                <span>Detail</span>
                                            </a>

                                            {{-- Hapus --}}
                                            <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm d-flex align-items-center gap-1">
                                                    <i class="fa fa-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">Data tagihan tidak tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {!! $models->links() !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

{{-- Dibuat oleh Umar Ulkhak --}}
