<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitProduk extends Model
{
    protected $table = "unit_produks";

    protected $fillable = [
        'sku',
        'nama_unit',
        'harga_modal',
        'stok_awal',
        'notes',
    ];
}
