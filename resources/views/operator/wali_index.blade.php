{{--
|--------------------------------------------------------------------------
| VIEW: Operator - Daftar Wali Murid (Dengan Bulk Delete)
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Fitur       :
|   - Bulk delete (hapus terpilih)
|   - Hapus semua (dengan konfirmasi)
|   - Tetap profesional & tidak lebay
|
--}}

@extends('layouts.app_sneat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold">{{ $title }}</h5>
                    <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary btn-sm px-3">
                        <i class="fa fa-plus me-1"></i> Tambah Data
                    </a>
                </div>
            </div>
            <div class="card-body">

                {{-- Pencarian --}}
                <div class="mb-4">
                    {!! Form::open([
                        'route' => $routePrefix . '.index',
                        'method' => 'GET',
                        'class' => 'row g-2'
                    ]) !!}
                        <div class="col-md-5">
                            <input
                                type="text"
                                name="q"
                                class="form-control form-control-sm"
                                placeholder="Cari nama atau email wali..."
                                value="{{ request('q') }}"
                                autocomplete="off"
                            >
                        </div>
                        <div class="col-md-7 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                                <i class="fa fa-search me-1"></i> Cari
                            </button>
                            @if(request('q'))
                                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                                    <i class="fa fa-times me-1"></i> Reset
                                </a>
                            @endif
                        </div>
                    {!! Form::close() !!}
                </div>

                {{-- Form Bulk Delete --}}
                {!! Form::open([
                    'route' => $routePrefix . '.bulk-destroy',
                    'method' => 'DELETE',
                    'id' => 'bulk-delete-form',
                    'style' => 'display:none;'
                ]) !!}
                    @csrf
                {!! Form::close() !!}

                {{-- Form Hapus Semua --}}
                {!! Form::open([
                    'route' => $routePrefix . '.destroy-all',
                    'method' => 'DELETE',
                    'id' => 'destroy-all-form',
                    'style' => 'display:none;'
                ]) !!}
                    @csrf
                    <input type="hidden" name="confirm" value="hapus-semua-wali">
                {!! Form::close() !!}

                {{-- Tombol Aksi Massal --}}
                <div class="d-flex gap-2 mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select-all">
                        <label class="form-check-label" for="select-all">Pilih Semua</label>
                    </div>

                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-bulk-delete" disabled>
                        <i class="fa fa-trash me-1"></i> Hapus Terpilih
                    </button>

                    <button type="button" class="btn btn-outline-dark btn-sm" id="btn-destroy-all">
                        <i class="fa fa-ban me-1"></i> Hapus Semua
                    </button>
                </div>

                {{-- Tabel Data --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="select-header" style="margin-top: 0;">
                                    </div>
                                </th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Email</th>
                                <th>Akses</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($models as $item)
                                <tr>
                                    <td>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input bulk-item" type="checkbox" name="ids[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td class="fw-medium">{{ $item->name }}</td>
                                    <td>{{ $item->nohp ?: '–' }}</td>
                                    <td class="text-muted">{{ $item->email }}</td>
                                    <td>
                                        <span class="badge bg-label-success rounded-pill px-3 py-1">
                                            {{ ucfirst($item->akses) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route($routePrefix . '.show', $item->id) }}"
                                               class="btn btn-icon btn-outline-info btn-sm"
                                               title="Detail {{ $item->name }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route($routePrefix . '.edit', $item->id) }}"
                                               class="btn btn-icon btn-outline-warning btn-sm"
                                               title="Edit {{ $item->name }}">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route($routePrefix . '.destroy', $item->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin menghapus wali: {{ $item->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-icon btn-outline-danger btn-sm"
                                                        title="Hapus {{ $item->name }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Tidak ada data wali ditemukan.
                                        @if(request('q'))
                                            <br><a href="{{ route($routePrefix . '.index') }}" class="text-primary mt-1 d-inline">Lihat semua data</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($models->hasPages())
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $models->firstItem() }}–{{ $models->lastItem() }} dari {{ $models->total() }} data
                        </small>
                        <div>
                            {{ $models->links() }}
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f9fafb !important;
    }
    .btn-icon {
        width: 36px;
        height: 36px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    .btn-icon i { margin: 0; }
    .btn-icon:hover { transform: scale(1.05); transition: transform 0.1s ease; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all');
    const selectHeader = document.getElementById('select-header');
    const bulkItems = document.querySelectorAll('.bulk-item');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const btnDestroyAll = document.getElementById('btn-destroy-all');
    const bulkDeleteForm = document.getElementById('bulk-delete-form');
    const destroyAllForm = document.getElementById('destroy-all-form');

    // Sync header checkbox
    selectHeader.addEventListener('change', () => {
        selectAll.checked = selectHeader.checked;
        bulkItems.forEach(el => el.checked = selectHeader.checked);
        updateBulkButton();
    });

    // Sync "Pilih Semua"
    selectAll.addEventListener('change', () => {
        bulkItems.forEach(el => el.checked = selectAll.checked);
        updateBulkButton();
    });

    // Update status tombol hapus terpilih
    bulkItems.forEach(el => {
        el.addEventListener('change', updateBulkButton);
    });

    function updateBulkButton() {
        const checked = document.querySelectorAll('.bulk-item:checked').length;
        btnBulkDelete.disabled = checked === 0;
    }

    // Hapus terpilih
    btnBulkDelete.addEventListener('click', () => {
        const checked = document.querySelectorAll('.bulk-item:checked');
        if (checked.length === 0) return;

        if (!confirm(`Yakin ingin menghapus ${checked.length} wali yang dipilih?`)) return;

        const form = bulkDeleteForm;
        // ✅ Pastikan _method=DELETE tetap ada!
        form.innerHTML = `
            @csrf
            <input type="hidden" name="_method" value="DELETE">
        `;

        checked.forEach(el => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = el.value;
            form.appendChild(input);
        });

        form.submit();
    });

    // Hapus semua
    btnDestroyAll.addEventListener('click', () => {
        if (!confirm('⚠️ PERINGATAN: Ini akan menghapus SEMUA data wali!\n\nKetik "HAPUS SEMUA" di bawah untuk konfirmasi.')) return;

        const userInput = prompt('Ketik "HAPUS SEMUA" untuk melanjutkan:');
        if (userInput && userInput.trim().toUpperCase() === 'HAPUS SEMUA') {
            if (confirm('⚠️ Terakhir kali: Yakin ingin menghapus SEMUA wali?')) {
                destroyAllForm.submit();
            }
        } else {
            alert('Konfirmasi gagal. Operasi dibatalkan.');
        }
    });

    // Fokus pencarian
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput && !searchInput.value) searchInput.focus();
});
</script>
@endpush
