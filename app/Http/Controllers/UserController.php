<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data pengguna (admin & operator).
 * 
 * Dibuat oleh Umar Ulkhak
 */
class UserController extends Controller
{
    /**
     * Menampilkan daftar user (kecuali yang memiliki akses 'wali').
     */
    public function index()
    {
        $models = User::where('akses', '<>', 'wali')
                      ->latest()
                      ->paginate(50);

        return view('operator.user_index', compact('models'));
    }

    /**
     * Menampilkan form untuk menambahkan user baru.
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
     * Menyimpan data user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'akses'    => 'required|in:operator,admin',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = bcrypt($request->password);
        $validated['email_verified_at'] = now();

        User::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route('user.index');
    }

    /**
     * (Opsional) Menampilkan detail user tertentu.
     */
    public function show($id)
    {
        //
    }

    /**
     * Menampilkan form edit user berdasarkan ID.
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
     * Memperbarui data user berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'akses'    => 'required|in:operator,admin,wali',
            'password' => 'nullable|string|min:6',
        ]);

        // Hanya enkripsi password jika diisi
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route('user.index');
    }

    /**
     * Menghapus user berdasarkan ID.
     * Mencegah penghapusan akun penting.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cegah penghapusan akun penting
        if ($user->email === 'alkhak24@gmail.com') {
            flash('Akun ini tidak dapat dihapus.')->error();
            return redirect()->route('user.index');
        }

        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route('user.index');
    }
}