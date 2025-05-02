<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTotalPrice extends Model
{
    protected $table = 'order_total_price';

    protected $fillable = [
        'order_id',
        'total_price',
        'created_at',
        'updated_at',
    ];
}
