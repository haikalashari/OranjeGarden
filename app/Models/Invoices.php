<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoices extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'batch_number',
        'invoice_pdf_path',
    ];
}
