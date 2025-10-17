<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'total',
    ];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
