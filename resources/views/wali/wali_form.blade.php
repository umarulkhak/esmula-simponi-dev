{{--
|--------------------------------------------------------------------------
| VIEW: Wali - Form Profil
|--------------------------------------------------------------------------
| Penulis     : Umar Ulkhak
| Tujuan      : Menampilkan form tambah/edit profil wali murid.
| Fitur       :
|   - Form dinamis (create/update)
|   - Validasi error dengan feedback visual
|   - Password opsional saat edit
|   - Tombol aksi: Simpan & Batal
|   - Responsif & sesuai gaya Sneat
|   - ID tidak ditampilkan ke pengguna
--}}

@extends('layouts.app_sneat_wali')

@section('content')
<div class="row">
    <div class="col-12 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bx bx-edit-alt me-2"></i>{{ $title }}
                </h5>
                {{-- ID sengaja disembunyikan untuk privasi & UX yang lebih bersih --}}
            </div>
            <div class="card-body">

                {!! Form::model($model, [
                    'route'  => $route,
                    'method' => $method,
                    'class'  => 'needs-validation',
                    'novalidate'
                ]) !!}

                <div class="row g-3">

                    {{-- Nama Lengkap --}}
                    <div class="col-12">
                        <label for="name" class="form-label fw-semibold">
                            <i class="bx bx-user me-2 text-muted"></i>Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        {!! Form::text('name', null, [
                            'class'       => 'form-control' . ($errors->has('name') ? ' is-invalid' : ''),
                            'id'          => 'name',
                            'placeholder' => 'Contoh: Budi Santoso',
                            'required',
                            'autofocus'
                        ]) !!}
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-12">
                        <label for="email" class="form-label fw-semibold">
                            <i class="bx bx-envelope me-2 text-muted"></i>Email <span class="text-danger">*</span>
                        </label>
                        {!! Form::email('email', null, [
                            'class'       => 'form-control' . ($errors->has('email') ? ' is-invalid' : ''),
                            'id'          => 'email',
                            'placeholder' => 'budi@email.com',
                            'required'
                        ]) !!}
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- No. HP --}}
                    <div class="col-12">
                        <label for="nohp" class="form-label fw-semibold">
                            <i class="bx bx-phone me-2 text-muted"></i>No. HP <span class="text-danger">*</span>
                        </label>
                        {!! Form::text('nohp', null, [
                            'class'       => 'form-control' . ($errors->has('nohp') ? ' is-invalid' : ''),
                            'id'          => 'nohp',
                            'placeholder' => '081234567890',
                            'required'
                        ]) !!}
                        @error('nohp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="col-12">
                        <label for="password" class="form-label fw-semibold">
                            <i class="bx bx-lock me-2 text-muted"></i>
                            {{ $method === 'POST' ? 'Password' : 'Password (Opsional)' }}
                            @if($method === 'POST') <span class="text-danger">*</span> @endif
                        </label>
                        {!! Form::password('password', [
                            'class'       => 'form-control' . ($errors->has('password') ? ' is-invalid' : ''),
                            'id'          => 'password',
                            'placeholder' => '••••••••',
                            $method === 'POST' ? 'required' : 'autocomplete' => 'new-password'
                        ]) !!}
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($method === 'PUT')
                            <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
                        @endif
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div class="col-12">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            <i class="bx bx-lock-open me-2 text-muted"></i>
                            {{ $method === 'POST' ? 'Konfirmasi Password' : 'Konfirmasi Password (Opsional)' }}
                            @if($method === 'POST') <span class="text-danger">*</span> @endif
                        </label>
                        {!! Form::password('password_confirmation', [
                            'class'       => 'form-control' . ($errors->has('password_confirmation') ? ' is-invalid' : ''),
                            'id'          => 'password_confirmation',
                            'placeholder' => '••••••••',
                            $method === 'POST' ? 'required' : 'autocomplete' => 'new-password'
                        ]) !!}
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- Tombol Aksi --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>{{ $button }}
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>Batal
                    </a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endpush
