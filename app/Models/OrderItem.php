<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'plant_id',
        'scanned_by',
        'quantity',
        'scanned_qty'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'scanned_qty' => 'integer',
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

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
