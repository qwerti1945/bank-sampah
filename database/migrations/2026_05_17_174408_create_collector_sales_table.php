<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Membuat Tabel Induk (Header) Penjualan Pengepul
        Schema::create('collector_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users'); // Admin/Staf yang menjual
            $table->date('sold_at'); // Tanggal transaksi penjualan rill ke pengepul
            $table->decimal('total_amount', 12, 2); // Total uang masuk hasil penjualan global
            $table->string('photo_proof')->nullable(); // Nota/Kwitansi bukti fisik dari pengepul
            $table->timestamps();
        });

        // 2. Membuat Tabel Rincian (Detail) Barang yang Dijual
        Schema::create('collector_sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collector_sale_id')
                  ->constrained('collector_sales')
                  ->onDelete('cascade'); // Jika nota induk dihapus, rincian otomatis terhapus
            $table->foreignId('waste_id')->constrained('wastes')->onDelete('cascade');
            $table->decimal('qty', 10, 2); // Jumlah berat atau banyaknya barang yang dijual
            $table->decimal('price_per_unit', 12, 2); // Harga satuan dari pengepul besar (bisa beda dengan harga beli nasabah)
            $table->decimal('subtotal', 12, 2); // Hasil kali qty * price_per_unit
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // Drop tabel detail terlebih dahulu karena memiliki foreign key dependency
        Schema::dropIfExists('collector_sale_details');
        Schema::dropIfExists('collector_sales');
    }
};