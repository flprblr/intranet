<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MvOrder extends Model
{
    use HasFactory;
    protected $table = 'mv_orders';

    public function warehouse()
    {
        return $this->belongsTo(MvWarehouse::class, 'id_warehouse');
    }
    
}
