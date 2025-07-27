<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan daftar user (kecuali wali).
     */
    public function index()
    {
        $models = User::where('akses', '<>', 'wali')->latest()->paginate(50);
        return view('operator.user_index', compact('models'));
    }

    /**
     * Tampilkan form tambah user.
     */
    public function create()
    {
        return view('operator.user_form', [
            'model'  => new User(),
            'method' => 'POST',
            'route'  => 'user.store',
            'button' => 'SIMPAN',
        ]);
    }

    /**
     * Simpan data user baru.
     */
    public function store(Request $request)
    {
        $requestData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'akses'    => 'required|in:operator,admin',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $requestData['password'] = bcrypt($request->password);
        $requestData['email_verified_at'] = now();

        User::create($requestData);

        flash('Data berhasil disimpan')->success();
        return redirect()->route('user.index');
    }

    /**
     * Tampilkan detail user (jika diperlukan).
     */
    public function show($id)
    {
        //
    }

    /**
     * Tampilkan form edit user.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update data user.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        //
    }
}