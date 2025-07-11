<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'barang_masuks';

    protected $fillable = [
        'principle_subdealer_id',
        'nomor_barang_masuk',
        'tanggal',
        'remarks',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
