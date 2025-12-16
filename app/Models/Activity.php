<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activity extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'before',
        'after',
        'can_restore',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'can_restore' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

