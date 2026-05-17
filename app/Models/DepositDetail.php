<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositDetail extends Model
{
    protected $fillable = ['deposit_id', 'waste_id', 'qty', 'price_at_transaction', 'subtotal'];

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }

    public function waste()
    {
        return $this->belongsTo(Waste::class, 'waste_id');
    }
}