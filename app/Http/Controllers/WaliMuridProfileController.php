<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User as Model;
use Illuminate\Support\Facades\Hash;

class WaliMuridProfileController extends Controller
{
    private string $viewPath = 'wali.';
    private string $routePrefix = 'wali.profile';
    private string $viewIndex = 'wali_index';
    private string $viewForm  = 'wali_form';
    private string $viewShow  = 'profile_show';

    // 👇 wajib ada biar route index tidak error
    public function index()
    {
        $user = auth()->user();

        return view($this->viewPath . $this->viewIndex, [
            'model' => $user,
            'title' => 'Profil Wali Murid',
        ]);
    }

    public function edit()
    {
        $user = auth()->user();

        return view($this->viewPath . $this->viewForm, [
            'model'  => $user,
            'method' => 'PUT',
            'route'  => [$this->routePrefix . '.update', $user->id],
            'button' => 'UPDATE',
            'title'  => 'Edit Profil Wali Murid',
        ]);
    }


    public function update(Request $request, $id)
    {
        $user = Model::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'nohp'     => 'required|string|unique:users,nohp,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $namaLama = $user->name;
        $user->update($validated);

        $pesan = "✅ User berhasil diperbarui";
        $pesan .= ($namaLama !== $validated['name'])
            ? " ({$namaLama} → {$validated['name']})"
            : ": {$validated['name']}";

        flash($pesan)->success();

        return redirect()->route('wali.profile.index');
    }

    public function show($id)
    {
        // bisa disiapkan nanti kalau mau detail
    }
}
