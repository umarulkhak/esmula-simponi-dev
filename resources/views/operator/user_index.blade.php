@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">Data User</h5>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Akses</th>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Data tidak ada!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {!! $models->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection