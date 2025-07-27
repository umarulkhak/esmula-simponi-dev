@extends('layouts.app_sneat')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <h5 class="card-header">User Form</h5>

            <div class="card-body">
                {!! Form::model($model, [
                    'route' => $route,
                    'method' => $method
                ]) !!}

                {{-- Nama --}}
                <div class="form-group mb-3">
                    <label for="name">Nama</label>
                    {!! Form::text('name', null, ['class' => 'form-control', 'autofocus']) !!}
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                </div>

                {{-- Email --}}
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    {!! Form::email('email', null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                </div>

                {{-- No HP --}}
                <div class="form-group mb-3">
                    <label for="nohp">No HP</label>
                    {!! Form::text('nohp', null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('nohp') }}</span>
                </div>

                {{-- Akses --}}
                <div class="form-group mb-3">
                    <label for="akses">Akses</label>
                    {!! Form::select('akses', [
                        'admin' => 'Admin',
                        'operator' => 'Operator',
                        'wali' => 'Wali'
                    ], null, ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('akses') }}</span>
                </div>

                {{-- Password --}}
                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    {!! Form::password('password', ['class' => 'form-control']) !!}
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                </div>

                {{-- Konfirmasi Password --}}
                <div class="form-group mb-3">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                </div>

                {{-- Tombol Submit --}}
                <div class="form-group mt-4">
                    {!! Form::submit($button, ['class' => 'btn btn-primary']) !!}
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endsection