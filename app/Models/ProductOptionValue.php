<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $fillable = [
        'product_option_id', 'value', 'sku_suffix', 'price_offset', 'stock', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_offset' => 'decimal:2',
        ];
    }

    public function option()
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
