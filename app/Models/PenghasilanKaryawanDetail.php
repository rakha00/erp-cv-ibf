<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenghasilanKaryawanDetail extends Model
{
    use SoftDeletes;

    protected $table = "penghasilan_karyawan_details";

    protected $fillable = [
        'karyawan_id',
        'kasbon',
        'lembur',
        'bonus',
        'tanggal',
        'remarks',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
