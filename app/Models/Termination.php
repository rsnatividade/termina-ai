<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Termination extends Model
{
    protected $fillable = [
        'owner_phone',
        'group_id',
        'group_link',
        'status'
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(TerminationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function polls(): HasMany
    {
        return $this->hasMany(Poll::class);
    }
} 