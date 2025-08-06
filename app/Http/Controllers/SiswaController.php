<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Siswa as Model;
use App\Models\User;

/**
 * Controller untuk manajemen data Siswa.
 *
 * @author  Umar Ulkhak
 * @date    3 Agustus 2025
 */
class SiswaController extends Controller
{
    private string $viewPath    = 'operator.';
    private string $routePrefix = 'siswa';
    private string $viewIndex   = 'siswa_index';
    private string $viewForm    = 'siswa_form';
    private string $viewShow    = 'siswa_show';

    /**
     * Menampilkan daftar siswa.
     */
    public function index()
    {
        $models = Model::latest()->paginate(50);

        return view($this->viewPath . $this->viewIndex, [
            'models'      => $models,
            'routePrefix' => $this->routePrefix,
            'title'       => 'Data Siswa',
        ]);
    }

    /**
     * Menampilkan form tambah siswa.
     */
    public function create()
    {
        return view($this->viewPath . $this->viewForm, [
            'model'  => new Model(),
            'method' => 'POST',
            'route'  => $this->routePrefix . '.store',
            'button' => 'SIMPAN',
            'title'  => 'Form Data Siswa',
            'wali'   => User::where('akses', 'wali')->pluck('name', 'id'),
        ]);
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'wali_id'  => 'nullable|exists:users,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => 'required|digits:10|unique:siswas,nisn',
            'kelas'    => 'required|string|max:10',
            'angkatan' => 'required|integer|digits:4',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:5000',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
        }

        if ($request->filled('wali_id')) {
            $validated['wali_status'] = 'ok';
        }

        $validated['user_id'] = Auth::id();

        Model::create($validated);

        flash('Data berhasil disimpan')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan form edit siswa.
     */
    public function edit(int $id)
    {
        $siswa = Model::findOrFail($id);

        return view($this->viewPath . $this->viewForm, [
            'model'  => $siswa,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $siswa->id],
            'button' => 'UPDATE',
            'title'  => 'Form Data Siswa',
            'wali'   => User::where('akses', 'wali')->pluck('name', 'id'),
        ]);
    }

    /**
     * Memperbarui data siswa.
     */
    public function update(Request $request, int $id)
    {
        $siswa = Model::findOrFail($id);

        $validated = $request->validate([
            'wali_id'  => 'nullable|exists:users,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => "required|digits:10|unique:siswas,nisn,{$id}",
            'kelas'    => 'required|string|max:10',
            'angkatan' => 'required|integer|digits:4',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:5000',
        ]);

        if ($request->hasFile('foto')) {
            if (!empty($siswa->foto) && Storage::exists($siswa->foto)) {
                Storage::delete($siswa->foto);
            }

            $validated['foto'] = $request->file('foto')->store('public/foto_siswa');
        }

        if ($request->filled('wali_id')) {
            $validated['wali_status'] = 'ok';
        }

        $validated['user_id'] = Auth::id();

        $siswa->update($validated);

        flash('Data berhasil diperbarui')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menghapus data siswa.
     */
    public function destroy(int $id)
    {
        $siswa = Model::findOrFail($id);

        if (!empty($siswa->foto) && Storage::exists($siswa->foto)) {
            Storage::delete($siswa->foto);
        }

        $siswa->delete();

        flash('Data berhasil dihapus')->success();
        return redirect()->route($this->routePrefix . '.index');
    }

    /**
     * Menampilkan detail siswa.
     */
    public function show($id)
    {
        $siswa = Model::findOrFail($id);

        return view($this->viewPath . $this->viewShow, [
            'model'        => $siswa,
            'title'        => 'Detail Siswa',
            'routePrefix'  => $this->routePrefix,
        ]);

    }
}