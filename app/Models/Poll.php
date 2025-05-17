<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    protected $fillable = [
        'termination_id',
        'type',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function termination(): BelongsTo
    {
        return $this->belongsTo(Termination::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class);
    }
} 