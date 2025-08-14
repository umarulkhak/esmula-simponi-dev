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
    private string $viewPath = 'operator.';
    private string $routePrefix = 'user';

    private string $viewIndex = 'user_index';
    private string $viewForm  = 'user_form';
    private string $viewShow  = 'user_show'; // Disiapkan untuk kebutuhan mendatang

    /**
     * Menampilkan daftar user (kecuali wali).
     */
    public function index()
    {
        $users = Model::where('akses', '<>', 'wali')
                    ->latest()
                    ->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $users,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data User',
        ]);
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data User',
        ]);
    }

    /**
     * Simpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'akses'    => 'required|in:operator,admin,wali',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();

        Model::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * (Opsional) Detail user.
     */
    public function show($id)
    {
        // Siapkan jika ingin menampilkan detail
    }

    /**
     * Form edit user.
     */
    public function edit($id)
    {
        $user = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $user->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data User',
        ]);
    }

    /**
     * Update data user.
     */
    public function update(Request $request, $id)
    {
        $user = Model::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'akses'    => 'required|in:operator,admin,wali',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Hapus user (kecuali akun penting).
     */
    public function destroy($id)
    {
        $user = Model::findOrFail($id);

        if ($user->email === 'alkhak24@gmail.com') {
            flash('Akun ini tidak dapat dihapus.')->error();
            return redirect()->route($this->routePrefix . '.index');
        }

        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route($this->routePrefix . '.index');
    }
}
