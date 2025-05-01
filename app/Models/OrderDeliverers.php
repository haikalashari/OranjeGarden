<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDeliverers extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'batch_number',
        'delivery_photo',
        'status',
    ];
}
