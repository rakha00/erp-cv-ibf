<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = "barang_masuks";

    protected $fillable = [
        'principle_subdealer_id',
        'nomor_barang_masuk',
        'tanggal',
    ];

    public function principleSubdealer()
    {
        return $this->belongsTo(PrincipleSubdealer::class);
    }

    public function barangMasukDetails()
    {
        return $this->hasMany(BarangMasukDetail::class);
    }
}
