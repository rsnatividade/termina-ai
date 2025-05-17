<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'termination_id',
        'participant_id',
        'content',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    public function termination(): BelongsTo
    {
        return $this->belongsTo(Termination::class);
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(TerminationParticipant::class, 'participant_id');
    }
} 