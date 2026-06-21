<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'song_id',
        'reviewer_id',
        'title',
        'slug',
        'body',
        'cover_image_path',
        'score_melody',
        'score_lyrics',
        'score_production',
        'score_originality',
        'overall_score',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'overall_score' => 'decimal:2',
    ];

    protected $appends = ['cover_url'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (! $this->cover_image_path) {
            return null;
        }

        return str_starts_with($this->cover_image_path, 'http')
            ? $this->cover_image_path
            : Storage::url($this->cover_image_path);
    }
}
