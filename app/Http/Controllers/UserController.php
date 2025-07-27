<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen user (admin, operator).
 * Dibuat oleh Umar Ulkhak
 */
class UserController extends Controller
{
    /**
     * Tampilkan daftar user (kecuali yang memiliki akses 'wali').
     */
    public function index()
    {
        $models = User::where('akses', '<>', 'wali')
                      ->latest()
                      ->paginate(50);

        return view('operator.user_index', compact('models'));
    }

    /**
     * Tampilkan form tambah user baru.
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
     * Simpan data user baru ke database.
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
     * (Opsional) Tampilkan detail user tertentu.
     */
    public function show($id)
    {
        //
    }

    /**
     * Tampilkan form edit data user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('operator.user_form', [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => ['user.update', $user->id],
            'button' => 'UPDATE',
        ]);
    }

    /**
     * Update data user yang sudah ada.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $requestData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'akses'    => 'required|in:operator,admin,wali',
            'password' => 'nullable|string|min:6',
        ]);

        // Enkripsi password hanya jika diisi
        if (!empty($requestData['password'])) {
            $requestData['password'] = bcrypt($requestData['password']);
        } else {
            unset($requestData['password']);
        }

        $user->update($requestData);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route('user.index');
    }

    /**
     * Hapus user berdasarkan ID.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah penghapusan akun dengan email tertentu
        if ($user->email === 'alkhak24@gmail.com') {
            flash('Akun ini tidak dapat dihapus.')->error();
            return redirect()->route('user.index');
        }

        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route('user.index');
    }
}