{{-- resources/views/operator/user_index.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">{{ $title }}</h5>
            <div class="card-body">

                {{-- Tombol Tambah --}}
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm mb-3">
                    <i class="fa fa-plus me-1"></i> Tambah Data
                </a>

                {{-- Form Pencarian --}}
                {!! Form::open(['route' => $routePrefix . '.index', 'method' => 'GET']) !!}
                    <div class="input-group mb-3">
                        <input
                            type="text"
                            name="q"
                            class="form-control"
                            placeholder="Cari Nama Siswa"
                            value="{{ request('q') }}"
                        >
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                {!! Form::close() !!}

                {{-- Tabel Data --}}
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Wali Murid</th>
                                <th>Nama Siswa</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Angkatan</th>
                                <th>Created By</th>
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration + ($models->firstItem() - 1) }}</td>
                                    <td>{{ $item->wali->name }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->nisn }}</td>
                                    <td>{{ $item->kelas }}</td>
                                    <td>{{ $item->angkatan }}</td>
                                    <td>{{ $item->user->name }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            {{-- Edit --}}
                                            <a href="{{ route($routePrefix . '.edit', $item->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fa fa-edit me-1"></i> Edit
                                            </a>
                                            {{-- Detail --}}
                                            <a href="{{ route($routePrefix . '.show', $item->id) }}" class="btn btn-info btn-sm">
                                                <i class="fa fa-info me-1"></i> Detail
                                            </a>
                                            {{-- Hapus --}}
                                            <form
                                                action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">Data tidak tersedia.</td>
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
