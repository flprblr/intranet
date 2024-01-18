<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'product_id',
        'variation_id',
        'sku',
        'woo_stock',
        'sap_stock',
        'ecommerce_id',
    ];
}
