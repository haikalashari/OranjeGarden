<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_status';

    public $timestamps = false;

    protected $with = [
        'status_category'
    ];

    protected $fillable = [
        'order_id',
        'status_id',
        'created_at' 
    ];

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    public function status_category()
    {
        return $this->belongsTo(StatusCategory::class, 'status_id');
    }
}
