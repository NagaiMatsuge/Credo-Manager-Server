<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FtpAccess extends Model
{
    use HasFactory;
    protected $table = 'ftp_access';
    protected $fillable = [
        'server_id',
        'port',
        'host',
        'login',
        'password',
        'description',
        'created_at',
        'updated_at'
    ];
}
