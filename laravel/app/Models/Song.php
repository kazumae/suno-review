<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Song extends Model
{
    use HasFactory;

    protected $fillable = [
        'submitted_by',
        'title',
        'artist_name',
        'slug',
        'suno_url',
        'youtube_url',
        'genre',
        'tags',
        'cover_image_path',
        'description',
        'status',
        'view_count',
        'is_featured',
        'featured_position',
        'published_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['cover_url', 'suno_embed_url', 'youtube_embed_url'];

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
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

    public function getSunoEmbedUrlAttribute(): ?string
    {
        if ($this->suno_url && preg_match('#suno\.com/song/([\w-]+)#', $this->suno_url, $m)) {
            return "https://suno.com/embed/{$m[1]}";
        }

        return null;
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if ($this->youtube_url && preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([\w-]{11})#', $this->youtube_url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }

        return null;
    }
}
