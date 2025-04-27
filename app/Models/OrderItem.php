<?php

namespace App\Models;

use App\Models\User;
use App\Models\Order;
use App\Models\Plant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'plant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public $timestamps = false; // Since we're manually using created_at

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at'
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }
}
