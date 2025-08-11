{{-- resources/views/operator/user_index.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">

        <div class="card">
            <h5 class="card-header">{{ $title }}</h5>

            <div class="card-body">

                {{-- Tombol tambah --}}
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm mb-3">
                <i class="fa fa-plus me-1"></i> Tambah Data
                </a>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Akses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td>{{ $loop->iteration + ($models->firstItem() - 1) }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->nohp ?? '-' }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ ucfirst($item->akses) ?? '-' }}</td>
                                    <td class="d-flex gap-1">
                                        {{-- Edit --}}
                                        <a href="{{ route($routePrefix . '.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fa fa-edit me-1"></i> Edit
                                        </a>
                                        {{-- Show --}}
                                        <a href="{{ route($routePrefix . '.show', $item->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-edit me-1"></i> Detail
                                        </a>
                                        {{-- Hapus --}}
                                        <form 
                                            action="{{ route($routePrefix . '.destroy', $item->id) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">
                                                <i class="fa fa-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Data tidak tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {!! $models->links() !!}
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

{{-- Dibuat oleh Umar Ulkhak --}}