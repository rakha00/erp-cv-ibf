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
        'sku',
        'nama_unit',
        'harga_modal',
        'harga_jual',
        'jumlah_keluar',
        'total_modal',
        'total_harga_jual',
        'keuntungan',
        'remarks',
    ];
}
