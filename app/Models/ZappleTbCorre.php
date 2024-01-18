<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZappleTbCorre extends Model
{
    use HasFactory;
    protected $table = 'zapple_tb_corre';
    protected $fillable = ['correlativo', 'fecha', 'tipo'];
}
