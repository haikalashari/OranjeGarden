<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCategory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'status_category';

    protected $fillable = ['status'];
}
