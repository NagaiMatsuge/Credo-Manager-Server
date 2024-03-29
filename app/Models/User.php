<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Traits\UuidsTrait;
use App\Traits\UserQuery;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, UuidsTrait, UserQuery;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'pause_start_time',
        'pause_end_time',
        'working_days',
        'phone',
        'work_start_time',
        'work_end_time',
        'manager_id',
        'developer',
        'phone',
        'color',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'working_days' => 'array',
        'pause_start_time' => 'date:hh:mm',
        'pause_end_time' => 'date:hh:mm',
        'work_start_time' => 'date:hh:mm',
        'work_end_time' => 'date:hh:mm',
    ];

    public function tasks()
    {
        return $this->hasMany(TaskUser::class, 'user_id', 'id');
    }
}
