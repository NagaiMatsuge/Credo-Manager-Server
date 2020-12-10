<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbAccess extends Model
{
    use HasFactory;
    protected $table = 'db_access'; 
}
