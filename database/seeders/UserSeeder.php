<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@banksampah.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '081111111111',
            'address' => 'Kantor Pusat Bank Sampah',
            'email_verified_at' => now(),
        ]);

        // 2. Akun Admin / Teller
        User::create([
            'name' => 'Admin Teller',
            'email' => 'admin@banksampah.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '082222222222',
            'address' => 'Cabang Bank Sampah',
            'email_verified_at' => now(),
        ]);

        // 3. Akun Nasabah
        User::create([
            'name' => 'Budi Nasabah',
            'email' => 'nasabah@banksampah.com',
            'password' => Hash::make('password'),
            'role' => 'nasabah',
            'balance' => 0, // Default saldo awal
            'phone' => '083333333333',
            'address' => 'Jl. Mawar No. 123',
            'email_verified_at' => now(),
        ]);
    }
}