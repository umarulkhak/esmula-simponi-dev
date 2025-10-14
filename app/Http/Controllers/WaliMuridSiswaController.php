<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;
use Illuminate\Support\Facades\Storage;

class WaliMuridSiswaController extends Controller
{
    public function index()
    {
        $models = Auth::user()->siswa()
            ->with(['wali', 'user'])
            ->paginate(10);

        return view('wali.siswa_index', [
            'models' => $models,
            'routePrefix' => 'wali.siswa',
        ]);
    }

    public function show($id)
    {
        $model = Siswa::with(['wali', 'user'])->findOrFail($id);

        if ($model->wali_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        return view('wali.siswa_show', compact('model'));
    }

    public function edit($id)
    {
        $model = Siswa::findOrFail($id);

        if ($model->wali_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('wali.siswa_form', [
            'model' => $model,
            'title' => 'Edit Data Siswa',
            'route' => ['wali.siswa.update', $model->id],
            'method' => 'PUT',
            'button' => 'Simpan Perubahan',
        ]);
    }

    public function update(Request $request, $id)
    {
        $model = Siswa::findOrFail($id);

        if ($model->wali_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $data = $request->only(['nama']);

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($model->foto && Storage::disk('public')->exists($model->foto)) {
                Storage::disk('public')->delete($model->foto);
            }

            // Simpan ke folder: foto_siswa (tanpa 'public/' di depan)
            $data['foto'] = $request->file('foto')->store('foto_siswa', 'public');
        }

        $model->update($data);

        return redirect()->route('wali.siswa.index')
                        ->with('success', 'Data siswa berhasil diperbarui.');
    }
}
