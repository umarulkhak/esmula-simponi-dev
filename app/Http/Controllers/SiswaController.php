<?php

namespace App\Http\Controllers;

use App\Models\Siswa as Model;
use Illuminate\Http\Request;

/**
 * Controller untuk manajemen data Siswa.
 * Dibuat oleh Umar Ulkhak
 */
class SiswaController extends Controller
{
    private string $viewPath    = 'operator.';
    private string $routePrefix = 'siswa';

    private string $viewIndex   = 'siswa_index';
    private string $viewForm    = 'siswa_form';
    private string $viewShow    = 'siswa_show'; // Disiapkan untuk kebutuhan mendatang

    /**
     * Menampilkan daftar Siswa.
     */
    public function index()
    {
        $models = Model::latest()
            ->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Siswa',
        ]);
    }

    /**
     * Menampilkan form tambah Siswa.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data Wali Murid',
        ]);
    }

    /**
     * Menyimpan data Siswa baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'nohp'     => 'required|string|unique:users,nohp',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['email_verified_at'] = now();
        $validated['akses'] = 'wali';

        Model::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit Siswa.
     */
    public function edit($id)
    {
        $user = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $user->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data Wali Murid',
        ]);
    }

    /**
     * Memperbarui data Siswa.
     */
    public function update(Request $request, $id)
    {
        $user = Model::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'password' => 'nullable|string|min:6',
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
     * Menghapus siswa berdasarkan ID.
     */
    public function destroy($id)
    {
        $user = Model::where('akses', 'wali')->findOrFail($id);
        $user->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * (Opsional) Menampilkan detail user.
     */
    public function show($id)
    {
        // Untuk kebutuhan mendatang
    }
}