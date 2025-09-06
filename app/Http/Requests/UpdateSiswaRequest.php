<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk validasi data saat memperbarui data siswa.
 *
 * Mengatur aturan validasi untuk setiap field yang dikirim dari form edit siswa.
 * Validasi mencakup: keberadaan, format, keunikan (dengan pengecualian record sendiri),
 * dan konsistensi dengan pilihan yang tersedia di UI (dropdown).
 *
 * @author  Umar Ulkhak
 * @date    5 April 2025
 */
class UpdateSiswaRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan mengedit data siswa.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // ✅ Diizinkan untuk semua user yang login
        // 🔧 Bisa dikembangkan dengan policy jika butuh role-based authorization
        return true;
    }

    /**
     * Mendefinisikan aturan validasi untuk setiap field.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        // ✅ Ambil ID dari route parameter 'siswa' — karena route resource Laravel menggunakan {siswa}
        // Contoh URL: /operator/siswa/5/edit → $this->route('siswa') = 5
        $id = $this->route('siswa');

        return [
            // ID Wali Murid — opsional, harus ada di tabel users jika diisi
            'wali_id' => [
                'nullable',
                'exists:users,id',
            ],

            // Nama Siswa — wajib, string, maksimal 255 karakter
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            // NISN — wajib, 10 digit, unik (kecuali untuk record ini)
            'nisn' => [
                'required',
                'digits:10',
                Rule::unique('siswas', 'nisn')->ignore($id), // ✅ Abaikan record saat ini
            ],

            // Kelas — wajib, hanya boleh: VII, VIII, atau IX (sesuai dropdown UI)
            'kelas' => [
                'required',
                Rule::in(['VII', 'VIII', 'IX']), // ✅ Eksplisit dan aman
            ],

            // Tahun Angkatan — wajib, integer 4 digit, batasan realistis
            'angkatan' => [
                'required',
                'integer',
                'digits:4',
                'between:2020,' . (date('Y') + 2), // ✅ 2020 sampai tahun depan
            ],

            // Foto — opsional, harus gambar, format & ukuran dibatasi
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png', // ✅ Hanya format umum
                'max:5000', // 5MB
            ],
        ];
    }

    /**
     * Mendefinisikan pesan error kustom untuk setiap aturan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kelas.in'         => 'Kelas hanya boleh diisi dengan: VII, VIII, atau IX.',
            'nisn.unique'      => 'NISN ini sudah digunakan oleh siswa lain.',
            'angkatan.between' => 'Tahun angkatan harus antara 2020 dan ' . (date('Y') + 2) . '.',
            'foto.max'         => 'Ukuran foto maksimal 5MB.',
            'foto.mimes'       => 'Format foto hanya mendukung: JPG, JPEG, PNG.',
        ];
    }
}
