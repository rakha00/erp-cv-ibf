<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitProduk extends Model
{
    use SoftDeletes;

    protected $table = "unit_produks";

    protected $fillable = [
        'sku',
        'nama_unit',
        'harga_modal',
        'stok_awal',
        'remarks',
    ];

    public function barangMasukDetails()
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function transaksiProdukDetails()
    {
        return $this->hasMany(TransaksiProdukDetail::class);
    }
}

