<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'description',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }
}
