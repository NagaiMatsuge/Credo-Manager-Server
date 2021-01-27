<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tasks\Query;

class Task extends Model
{
    use HasFactory, Query;

    protected $fillable = [
        'title',
        'approved',
        'deadline',
        'step_id',
        'type',
        'time',
        'created_at',
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
