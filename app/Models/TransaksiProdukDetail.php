<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiProdukDetail extends Model
{
     use SoftDeletes;
     
    protected $table = "transaksi_produk_details";
}
