<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Inertia\Inertia;
use Inertia\Response;

class GenreController extends Controller
{
    public function show(string $genre): Response
    {
        $songs = Song::published()
            ->where('genre', $genre)
            ->withCount('reviews')
            ->withAvg('reviews', 'overall_score')
            ->latest('published_at')
            ->get();

        return Inertia::render('Genres/Show', [
            'genre' => $genre,
            'songs' => $songs,
        ]);
    }
}
