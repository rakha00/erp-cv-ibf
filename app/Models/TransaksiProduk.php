<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiProduk extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produks';

    protected $fillable = [
        'no_invoice',
        'no_surat_jalan',
        'tanggal',
        'remarks',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function transaksiProdukDetails()
    {
        return $this->hasMany(TransaksiProdukDetail::class);
    }
}
