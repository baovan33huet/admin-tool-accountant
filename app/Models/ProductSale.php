<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSale extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'sale_price',
        'sale_date',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
