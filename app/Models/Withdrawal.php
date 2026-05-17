<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = ['nasabah_id', 'admin_id', 'amount', 'photo_proof'];

    public function nasabah()
    {
        return $this->belongsTo(User::class, 'nasabah_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}