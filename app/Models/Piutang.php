<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piutang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'piutangs';

    protected $fillable = [
        'transaksi_produk_id',
        'jatuh_tempo',
        'status_pembayaran',
        'sudah_dibayar',
        'total_harga_modal',
        'foto',
        'remarks',
    ];

    protected $casts = [
        'foto' => 'array',
    ];

    public function transaksiProduk()
    {
        return $this->belongsTo(TransaksiProduk::class);
    }
}
