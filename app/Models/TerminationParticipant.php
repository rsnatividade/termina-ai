<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TerminationParticipant extends Model
{
    const TYPE_TERMINATOR = 'terminator';
    const TYPE_TERMINATED = 'terminated';
    const TYPE_AUDITORIUM = 'auditorium';

    protected $fillable = [
        'termination_id',
        'phone',
        'participant_jid',
        'name',
        'token',
        'type'
    ];

    /**
     * Get the termination that owns the participant.
     */
    public function termination(): BelongsTo
    {
        return $this->belongsTo(Termination::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'participant_id');
    }
} 