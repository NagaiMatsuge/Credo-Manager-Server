<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'project_id',
        'price',
        'currency_id',
        'payment_type',
        'payment-date',
        'finished',
        'approved',
        'time_left'
    ];
}
