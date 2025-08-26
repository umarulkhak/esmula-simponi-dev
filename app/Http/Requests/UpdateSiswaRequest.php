<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('siswa'); // ambil ID dari route parameter

        return [
            'wali_id'  => 'nullable|exists:users,id',
            'nama'     => 'required|string|max:255',
            'nisn'     => [
                'required',
                'digits:10',
                Rule::unique('siswas', 'nisn')->ignore($id),
            ],
            'kelas'    => 'required|string|max:10',
            'angkatan' => 'required|integer|digits:4',
            'foto'     => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:5000',
        ];
    }
}
