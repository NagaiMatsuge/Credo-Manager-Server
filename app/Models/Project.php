<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'server_id',
        'title',
        'description',
        'deadline',
        'color',
        'photo'
    ];

    public $timestamps = false;

    protected $dateFormat = 'd-m-Y';
}
