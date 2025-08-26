<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // semua user yg login boleh, bisa disesuaikan dengan policy
    }

    public function rules(): array
    {
        return [
            'wali_id'  => 'nullable|exists:users,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => 'required|digits:10|unique:siswas,nisn',
            'kelas'    => 'required|string|max:10',
            'angkatan' => 'required|integer|digits:4',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:5000',
        ];
    }
}
