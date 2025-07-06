<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasukDetail extends Model
{
    use SoftDeletes;

    protected $table = "barang_masuk_details";

    protected $fillable = [
        "barang_masuk_id",
        "unit_produk_id",
        "harga_modal",
        "jumlah_barang_masuk",
        "remarks",
    ];

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function unitProduk()
    {
        return $this->belongsTo(UnitProduk::class);
    }
}
