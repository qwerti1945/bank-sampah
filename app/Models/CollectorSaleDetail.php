<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectorSaleDetail extends Model
{
    protected $fillable = ['collector_sale_id', 'waste_id', 'qty', 'price_per_unit', 'subtotal'];

    // Relasi balik ke nota induk
    public function collectorSale()
    {
        return $this->belongsTo(CollectorSale::class, 'collector_sale_id');
    }

    // Relasi untuk mengambil nama dan unit komoditas sampah
    public function waste()
    {
        return $this->belongsTo(Waste::class, 'waste_id');
    }
}