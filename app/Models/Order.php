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
        'rental_duration',
        'delivery_address',
        'total_price',
        'payment_status',
        'payment_proof',
        'delivery_status',
        'delivery_photo',
        'assigned_deliverer_id'
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
}
