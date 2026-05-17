<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'unit', 'current_price'];

    // Relasi historis sampah masuk dari nasabah
    public function depositDetails()
    {
        return $this->hasMany(DepositDetail::class, 'waste_id');
    }

    /**
     * TAMBAHKAN RELASI INI
     * Relasi historis sampah keluar ke pengepul besar
     */
    public function collectorSaleDetails()
    {
        return $this->hasMany(CollectorSaleDetail::class, 'waste_id');
    }
}