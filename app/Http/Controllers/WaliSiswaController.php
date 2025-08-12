<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaliSiswaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'wali_id' => 'required|exists:users,id',
            'siswa_id' => 'required'
        ]);

        $siswa = \App\Models\Siswa::findOrFail($request->siswa_id);
        $siswa->wali_id = $request->wali_id;
        $siswa->wali_status = 'ok';
        $siswa->save();

        flash('Data sudah ditambahkan')->success();
        return redirect()->route('wali.show', $request->wali_id);
    }

    public function update(Request $request, $id)
    {
        $siswa = \App\Models\Siswa::findOrFail($id);
        $waliId = $siswa->wali_id; // Simpan wali lama untuk redirect

        $siswa->wali_id = null;
        $siswa->wali_status = null;
        $siswa->save();

        flash('Data sudah dihapus')->success();
        return redirect()->route('wali.show', $waliId);
    }
}
