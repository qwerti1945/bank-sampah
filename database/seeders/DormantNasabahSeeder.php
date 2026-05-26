<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DormantNasabahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // =========================================================================
        // 1. Skenario: 10 Nasabah Non-Aktif > 6 Bulan (Terakhir Aktif ~7 Bulan Lalu)
        // =========================================================================
        $nasabah6Bulan = [
            'Ahmad Subagja', 'Rian Hidayat', 'Dewi Lestari', 'Eko Prasetyo', 'Siti Rahma',
            'Bambang Wijaya', 'Mega Utami', 'Aditya Putra', 'Fitriani', 'Denny Setiawan'
        ];

        foreach ($nasabah6Bulan as $index => $name) {
            // Set waktu aktivitas terakhir ke 7 bulan lalu + variasi tanggal acak
            $waktuPasif = Carbon::now()->subMonths(7)->subDays(rand(1, 15));

            DB::table('users')->insert([
                'name' => $name,
                'email' => 'nasabah.pasif6m' . ($index + 1) . '@gudang.com',
                'password' => Hash::make('password'), // Password bawaan: password
                'role' => 'nasabah',
                'balance' => rand(150000, 600000), // Saldo wajib > 0 agar dana mengendap terhitung
                'phone' => '0812' . rand(10000000, 99999999),
                'address' => 'Jl. Pendidikan Gg. ' . rand(1, 10) . ' No. ' . rand(11, 99) . ', Samarinda',
                'created_at' => $waktuPasif->copy()->subMonths(3), // Terdaftar 10 bulan lalu
                'updated_at' => $waktuPasif, // DIKUNCI: Penentu tanggal Aktivitas Terakhir di UI
            ]);
        }

        // =========================================================================
        // 2. Skenario: 10 Nasabah Non-Aktif > 3 Bulan (Terakhir Aktif ~4 Bulan Lalu)
        // =========================================================================
        $nasabah3Bulan = [
            'Andi Wijaya', 'Budi Santoso', 'Chandra Kirana', 'Dina Mariana', 'Fajar Nusantara',
            'Gita Gutawa', 'Hendra Wijaya', 'Indah Permata', 'Joko Susilo', 'Kartika Putri'
        ];

        foreach ($nasabah3Bulan as $index => $name) {
            // Set waktu aktivitas terakhir ke 4 bulan lalu + variasi tanggal acak
            $waktuPasif = Carbon::now()->subMonths(4)->subDays(rand(1, 15));

            DB::table('users')->insert([
                'name' => $name,
                'email' => 'nasabah.pasif3m' . ($index + 1) . '@gudang.com',
                'password' => Hash::make('password'),
                'role' => 'nasabah',
                'balance' => rand(50000, 250000), // Saldo wajib > 0
                'phone' => '0853' . rand(10000000, 99999999),
                'address' => 'Jl. Perjuangan Kampus Faperta No. ' . rand(1, 50) . ', Samarinda',
                'created_at' => $waktuPasif->copy()->subMonths(2), // Terdaftar 6 bulan lalu
                'updated_at' => $waktuPasif, // DIKUNCI: Penentu tanggal Aktivitas Terakhir di UI
            ]);
        }
    }
}