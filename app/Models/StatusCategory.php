<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusCategory extends Model
{
    /* The table associated with the model.
    *
    * @var string
    */
   protected $table = 'status_category';
    /*
   
   * @var array
   */
  protected $fillable = [
      'status',
  ];

}
