<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrincipleSubdealer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'principle_subdealers';

    protected $fillable = [
        'nama',
        'sales',
        'no_hp',
        'notes',
    ];
}
