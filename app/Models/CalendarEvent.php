<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class CalendarEvent extends Model
{
    use HasFactory,
        SoftDeletes,
        LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'start_at',
        'end_at',
        'all_day',
        'color',
        'created_by',
        'status',
    ];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'description',
                'start_at',
                'end_at',
                'all_day',
                'color',
                'created_by',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function casts()
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'all_day' => 'boolean',
            'status' => 'boolean',

        ];
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
