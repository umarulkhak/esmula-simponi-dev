<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request untuk validasi data saat menambahkan siswa baru.
 *
 * Mengatur aturan validasi untuk setiap field yang dikirim dari form tambah siswa.
 * Validasi mencakup: keberadaan, format, dan keunikan data.
 *
 * @author  Umar Ulkhak
 * @date    5 April 2025
 */
class StoreSiswaRequest extends FormRequest
{
    /**
     * Menentukan apakah user diizinkan membuat data siswa.
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

            // NISN — wajib, 10 digit, harus unik di tabel siswas
            'nisn' => [
                'required',
                'digits:10',
                'unique:siswas,nisn',
            ],

            // Kelas — wajib, hanya boleh: VII, VIII, atau IX
            'kelas' => [
                'required',
                Rule::in(['VII', 'VIII', 'IX']), // ✅ Lebih aman dan eksplisit
            ],

            // Tahun Angkatan — wajib, integer 4 digit (misal: 2025)
            'angkatan' => [
                'required',
                'integer',
                'digits:4',
                'between:2020,' . (date('Y') + 2), // ✅ Batasan realistis
            ],

            // Foto — opsional, harus gambar, format & ukuran dibatasi
            'foto' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png', // ✅ Hanya terima format umum
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
            'kelas.in'        => 'Kelas hanya boleh diisi dengan: VII, VIII, atau IX.',
            'nisn.unique'     => 'NISN ini sudah terdaftar. Gunakan NISN lain.',
            'angkatan.between' => 'Tahun angkatan harus antara 2020 dan ' . (date('Y') + 2) . '.',
            'foto.max'        => 'Ukuran foto maksimal 5MB.',
            'foto.mimes'      => 'Format foto hanya mendukung: JPG, JPEG, PNG.',
        ];
    }
}
