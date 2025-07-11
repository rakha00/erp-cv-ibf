<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Utang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'utangs';

    protected $fillable = [
        'barang_masuk_id',
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

    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }
}
