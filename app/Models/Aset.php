<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aset extends Model
{
    use SoftDeletes;

    protected $table = 'asets';

    protected $fillable = [
        'nama_aset',
        'harga',
        'jumlah_aset',
    ];
}
