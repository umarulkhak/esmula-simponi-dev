<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Jalankan seeder untuk membuat user admin default
        $this->call(CreateDefaultAdminUserSeeder::class);

        // Tambahkan seeder lain di sini jika diperlukan, contoh:
        // $this->call(KelasSeeder::class);
        // $this->call(SiswaSeeder::class);
    }
}
