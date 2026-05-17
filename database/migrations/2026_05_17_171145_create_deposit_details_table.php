<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deposit_id')->constrained('deposits')->onDelete('cascade');
            $table->foreignId('waste_id')->constrained('wastes')->onDelete('cascade');
            $table->decimal('qty', 10, 2); 
            $table->decimal('price_at_transaction', 12, 2); 
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposit_details');
    }
};