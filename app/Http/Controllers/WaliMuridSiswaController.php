<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa;

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

        // Pastikan data milik wali yang sedang login
        if ($model->wali_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        // Kirim data ke view form edit
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
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB
        ]);

        $data = $request->only(['nama']);

        // Handle upload foto
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($model->foto && \Storage::exists($model->foto)) {
                \Storage::delete($model->foto);
            }
            $data['foto'] = $request->file('foto')->store('siswa/foto', 'public');
        }

        $model->update($data);

        return redirect()->route('wali.siswa.index')
                        ->with('success', 'Data siswa berhasil diperbarui.');
    }
}
