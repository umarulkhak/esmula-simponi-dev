<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        // Pastikan user ID 1 ada (sebagai pembuat data)
        if (!User::find(1)) {
            User::create([
                'name'     => 'Operator 2',
                'email'    => 'admin@sekolah.local',
                'password' => Hash::make('password123456'),
            ]);
        }

        // Hanya tambah 300 data baru (tidak menghapus data lama)
        Siswa::factory()->count(300)->create();

        $this->command->info('✅ 300 data siswa dummy berhasil ditambahkan!');
    }
}
