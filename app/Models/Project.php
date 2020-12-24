<?php

namespace App\Models;

use Egulias\EmailValidator\Warning\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    public function step()
    {
        return $this->hasMany(Step::class);
    }

    protected static function boot()
    {
        parent::boot();

        self::deleting(function ($model) {
            if ($model->photo)
                Storage::disk('public')->delete($model->photo);
        });
    }
}
