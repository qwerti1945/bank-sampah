<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Waste extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi secara massal, kecuali kolom 'id'
    protected $guarded = ['id'];
}