<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvWarehouse extends Model
{
    use HasFactory;
    protected $table = 'mv_warehouses';

    protected $fillable = [
        'id_warehouse',
        'description',
        'vkorg',
        'werks',
        'lgort',
        'vtweg',
        'des_sovos',
        'logo',
        'company_id',
        'active',
        // Otros campos asignables aquí
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
