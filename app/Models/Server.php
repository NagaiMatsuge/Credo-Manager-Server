<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'host'
    ];

    public function ftp_access()
    {
        return $this->hasMany(FtpAccess::class, 'server_id', 'id');
    }

    public function db_access()
    {
        return $this->hasMany(DbAccess::class, 'server_id', 'id');
    }
}
