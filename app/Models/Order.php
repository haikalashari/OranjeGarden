<?php

namespace App\Models;

use App\Models\User;
use App\Models\Customer;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_date',
        'end_date',
        'delivery_address',
        'payment_status',
        'payment_proof',
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(User::class, 'assigned_deliverer_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function status()
    {
        return $this->hasMany(OrderStatus::class, 'order_id')->orderBy('created_at', 'desc');
    }

    public function latestStatus()
    {
        return $this->hasOne(OrderStatus::class, 'order_id')->latest();
    }
}
