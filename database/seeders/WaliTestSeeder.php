<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WaliTestSeeder extends Seeder
{
    public function run()
    {
        foreach (range(1, 200) as $i) {
            User::create([
                'name' => 'Wali ' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'email' => "wali{$i}@sekolah.sch.id",
                'nohp' => '081234567' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'akses' => 'wali',
                'email_verified_at' => now(),
            ]);
        }
    }
}
