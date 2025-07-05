<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrincipleSubdealer extends Model
{
    protected $table = "principle_subdealers";

    protected $fillable = [
        'nama',
        'sales',
        'no_hp',
        'notes',
    ];
}
