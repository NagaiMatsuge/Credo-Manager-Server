<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'text',
        'task_id',
        'file'
    ];

    /**
     * Get the files for the message.
     */
    public function files()
    {
        return $this->hasMany(MessageFile::class, 'message_id', 'id');
    }
}
