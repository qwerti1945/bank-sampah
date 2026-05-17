<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectorSale extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'sold_at', 'total_amount', 'photo_proof'];

    // Cast otomatis string tanggal dari database menjadi objek carbon/date reaktif
    protected $casts = [
        'sold_at' => 'date'
    ];

    // Relasi ke Admin/Staf yang melakukan pencatatan transaksi
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relasi One-to-Many ke rincian barang yang dijual
    public function details()
    {
        return $this->hasMany(CollectorSaleDetail::class, 'collector_sale_id');
    }
}