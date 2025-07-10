<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'key_label',
        'value',
        'type',
        'description',
    ];

    public function getValueAttribute($value)
    {
        if (isset($this->type) && in_array($this->type, ['json', 'array'])) {
            return json_decode($value, true);
        }

        return $value;
    }
}
