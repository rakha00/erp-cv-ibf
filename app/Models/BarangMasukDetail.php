<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasukDetail extends Model
{
    protected $table = "barang_masuk_details";

    protected $fillable = [
        "barang_masuk_id",
        "unit_produk_id",
        "sku",
        "nama_unit",
        "harga_modal",
        "jumlah_barang_masuk",
        "notes",
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
