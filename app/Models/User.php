<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'balance',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2', // Mengonversi string decimal DB ke tipe data float/numeric di PHP dengan aman
        ];
    }

    // ==========================================
    // RELASI TABEL NASABAH
    // ==========================================

    /**
     * Relasi ke riwayat nota setoran sampah.
     * Satu Nasabah memiliki banyak Setoran.
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'nasabah_id');
    }

    /**
     * Relasi ke riwayat penarikan tunai saldo.
     * Satu Nasabah memiliki banyak Penarikan.
     */
    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'nasabah_id');
    }
}