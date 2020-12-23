<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'payment_date',
        'step_id',
        'currency_id',
        'amount',
        'payment_type'
    ];

    public $timestamps = false;
}
