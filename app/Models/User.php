<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        SoftDeletes,
        HasRoles,
        LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'email',
                'phone',
                'avatar',
                'status',
                'last_login',
            ])
            ->logOnlyDirty()
            ->dontLogIfAttributesChangedOnly([
                'last_login',
            ])
            ->dontSubmitEmptyLogs();
    }

    public function calendars()
    {
        return $this->hasMany(CalendarEvent::class, 'created_by');
    }
    public function news()
    {
        return $this->hasMany(News::class, 'created_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }
}
