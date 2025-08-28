<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request untuk validasi update data Biaya.
 */
class UpdateBiayaRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan melakukan request ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Semua user diizinkan, bisa disesuaikan dengan role/permission
        return true;
    }

    /**
     * Aturan validasi untuk update Biaya.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Nama wajib, unik di tabel 'biayas'
            // Kecuali untuk record saat ini (supaya tidak bentrok dengan dirinya sendiri)
            'nama'   => 'required|unique:biayas,nama,' . $this->biaya,

            // Jumlah wajib diisi
            'jumlah' => 'required',
        ];
    }

    /**
     * Pesan error kustom untuk validasi (opsional).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required'   => 'Nama biaya wajib diisi.',
            'nama.unique'     => 'Nama biaya sudah digunakan.',
            'jumlah.required' => 'Jumlah biaya wajib diisi.',
        ];
    }
}
