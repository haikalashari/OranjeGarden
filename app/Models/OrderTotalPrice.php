<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class OrderTotalPrice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'order_id',
        'billing_batch',
        'total_price',
        'created_at',
        'updated_at',
    ];
}
