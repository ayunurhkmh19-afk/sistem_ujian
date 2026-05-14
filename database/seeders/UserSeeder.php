<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Panitia (Admin)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sekolah.com',
            'password' => Hash::make('password'), // Password: password
            'role' => 'panitia',
        ]);

        // 2. Akun Pengawas
        User::create([
            'name' => 'Pak Budi (Pengawas)',
            'email' => 'guru@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'pengawas',
        ]);
        
        // Tambahan Pengawas lain
        User::create([
            'name' => 'Bu Siti (Pengawas)',
            'email' => 'siti@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'pengawas',
        ]);
    }
}