<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateDefaultAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek apakah user dengan email ini sudah ada (hindari duplikasi)
        if (!User::where('email', 'alkhak24@gmail.com')->exists()) {
            User::create([
                'name'     => 'Umar Ulkhak',
                'email'    => 'alkhak24@gmail.com',
                'password' => Hash::make('Khak240799'),
                'akses'    => 'admin',
                'nohp'     => '085711598638',
            ]);

            $this->command->info('✅ User admin "Umar Ulkhak" berhasil dibuat.');
        } else {
            $this->command->warn('⚠️  User dengan email alkhak24@gmail.com sudah ada. Tidak dibuat ulang.');
        }
    }
}
