<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Billable, Notifiable, HasApiTokens, LogsActivity;

    protected $fillable = [
        'username',
        'fullname',
        'image',
        'description',
        'skills',
        'email',
        'email_verified_at',
        'password',
        'socialite_id',
        'socialite_token',
        'socialite_refresh_token',
        'socialite_type'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    protected $dispatchesEvents = [
        'created' => \App\Events\NewUserRegisteredEvent::class
    ];

    protected static $recordEvents = ['created', 'updated'];
    
    public const SOCIAL_TYPES = [
        '1' => 'google',
        '2' => 'github'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['name', 'email'])
        ->useLogName('user')
        ->setDescriptionForEvent(fn(string $eventName) => request()->ip() );
    }

    public function getInAdminroles()
    {
        return $this->has('roles');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }
}
