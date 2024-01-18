<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saless extends Model
{
    use HasFactory;
    
    protected $table = 'saless';

    protected $fillable = [
        'FKDAT',
        'BUKRS',
        'VKORG',
        'VTWEG',
        'NAME1',
        'SALE'
    ];
}
