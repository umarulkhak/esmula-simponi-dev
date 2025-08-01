<?php

namespace App\Http\Controllers;

use App\Models\User as Model;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data pengguna (Admin & Operator).
 * Dibuat oleh Umar Ulkhak
 */
class UserController extends Controller
{
    // Konstanta view path untuk keperluan reusabilitas
    private string $viewPath = 'operator.';
    private string $routePrefix = 'user';

    // View names
    private string $viewIndex  = 'user_index';
    private string $viewForm   = 'user_form';
    private string $viewShow   = 'user_show'; // Disiapkan untuk kebutuhan mendatang

    /**
     * Menampilkan daftar user, kecuali yang memiliki akses 'wali'.
     */
    public function index()
    {
        return view($this->viewPath . $this->viewIndex, [
                    'models' => Model::where('akses','<>', 'wali')
                      ->latest()
                      ->paginate(50),
                      'routePrefix' => $this->routePrefix,
                      'title' => 'Data User'
        ]);
    }

    /**
     * Menampilkan form untuk menambahkan user baru.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data User'
        ]);
    }

    /**
     * Menyimpan data user baru ke dalam database.
     */
    public function store(Request $request)
    {
        // Validasi input user
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'akses'    => 'required|in:operator,admin',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Enkripsi password & tandai email terverifikasi
        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        // Simpan ke database
        Model::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * (Opsional) Menampilkan detail user tertentu.
     */
    public function show($id)
    {
        // Disiapkan jika suatu saat ingin menampilkan detail pengguna
    }

    /**
     * Menampilkan form edit user berdasarkan ID.
     */
    public function edit($id)
    {
        $user = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $user->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data User'
        ]);
    }

    /**
     * Memperbarui data user berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $user = Model::findOrFail($id);

        // Validasi input saat update
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'akses'    => 'required|in:operator,admin,wali',
            'password' => 'nullable|string|min:6',
        ]);

        // Update password jika disediakan
        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Simpan perubahan
        $user->update($validated);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus user berdasarkan ID.
     * Cegah penghapusan akun dengan email penting.
     */
    public function destroy($id)
    {
        $user = Model::findOrFail($id);

        // Cegah penghapusan akun penting
        if ($user->email === 'alkhak24@gmail.com') {
            flash('Akun ini tidak dapat dihapus.')->error();
            return redirect()->route($this->routePrefix . '.index');
        }

        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route($this->routePrefix . '.index');
    }
}