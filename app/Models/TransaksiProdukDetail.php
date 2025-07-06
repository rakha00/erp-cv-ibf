<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiProdukDetail extends Model
{
    use SoftDeletes;

    protected $table = "transaksi_produk_details";

    protected $fillable = [
        'transaksi_produk_id',
        'unit_produk_id',
        'harga_jual',
        'jumlah_keluar',
        'remarks',
    ];

    public function transaksiProduk()
    {
        return $this->belongsTo(TransaksiProduk::class);
    }

    public function unitProduk()
    {
        return $this->belongsTo(UnitProduk::class);
    }
}
