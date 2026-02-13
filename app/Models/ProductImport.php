<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'import_price',
        'import_date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

