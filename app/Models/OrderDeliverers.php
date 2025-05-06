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
        'delivery_batch',
        'delivery_photo',
        'status',
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
