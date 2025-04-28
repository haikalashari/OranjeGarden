<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_status';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'status_id',
        'created_at' 
    ];
}
