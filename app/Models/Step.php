<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id',
        'price',
        'debt',
        'currency_id',
        'payment_type',
        'payment_date'
    ];

    public $timestamps = false;
}
