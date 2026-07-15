<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantOptionValue extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'variant_id', 'option_value_id',
    ];
}
