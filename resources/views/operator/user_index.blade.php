{{-- resources/views/operator/user_index.blade.php --}}
@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">Data User</h5>

            <div class="card-body">
                {{-- Tombol tambah user --}}
                <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm mb-3">Tambah Data</a>

                <div class="table-responsive">
                    <table class="table table-striped">
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
                                    <td>{{ $item->akses ?? '-' }}</td>
                                    <td>
                                        {{-- Tombol Edit --}}
                                        <a href="{{ route('user.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('user.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">Data tidak ada!</td>
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