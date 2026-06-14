<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'song_id',
        'assigned_reviewer_id',
        'suno_url',
        'youtube_url',
        'title',
        'genre',
        'note',
        'status',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function assignedReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_reviewer_id');
    }
}
