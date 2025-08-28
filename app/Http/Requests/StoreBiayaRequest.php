<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request untuk validasi penyimpanan data Biaya baru.
 */
class StoreBiayaRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan melakukan request ini.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Untuk sekarang semua user diizinkan.
        // Bisa diubah sesuai kebutuhan (misal cek role/permission).
        return true;
    }

    /**
     * Aturan validasi untuk menyimpan data Biaya.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Nama biaya wajib diisi & harus unik di tabel 'biayas'
            'nama'   => 'required|unique:biayas,nama',

            // Jumlah wajib diisi (bisa tambahkan rule numeric kalau perlu)
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
