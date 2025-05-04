<?php

namespace App\Models;

use App\Models\User;
use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\OrderDeliverers;
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
        'total_price',
    ];

    protected $casts = [
        'order_date' => 'date',
        'end_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(OrderDeliverers::class);
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

    public function invoices()
    {
        return $this->hasMany(Invoices::class);
    }

    protected $appends = ['rental_duration'];

    public function getRentalDurationAttribute()
    {
        return \Carbon\Carbon::parse($this->order_date)
            ->diffInDays(\Carbon\Carbon::parse($this->end_date));
    }
}
