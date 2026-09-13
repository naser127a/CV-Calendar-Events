<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class News extends Model
{
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'title',
                'summary',
                'content',
                'image',
                'type',
                'source_type',
                'external_id',
                'external_url',
                'published_at',
                'status',
                'is_breaking',
                'breaking_until',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
    protected $fillable = [
        'title',
        'summary',
        'content',
        'image',
        'type',
        'source_type',
        'external_id',
        'external_url',
        'published_at',
        'status',
        'is_breaking',
        'breaking_until',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'breaking_until' => 'datetime',
            'status' => 'boolean',
            'is_breaking' => 'boolean',
        ];
    }
}
