<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function index(Request $request): Response
    {
        $q = trim((string) $request->query('q', ''));

        $songs = [];
        if ($q !== '') {
            $songs = Song::published()
                ->where(function ($query) use ($q) {
                    $query->where('title', 'like', "%{$q}%")
                        ->orWhere('artist_name', 'like', "%{$q}%")
                        ->orWhere('genre', 'like', "%{$q}%")
                        ->orWhere('tags', 'like', "%{$q}%");
                })
                ->withCount('reviews')
                ->withAvg('reviews', 'overall_score')
                ->latest('published_at')
                ->get();
        }

        return Inertia::render('Search/Index', [
            'q' => $q,
            'songs' => $songs,
        ]);
    }
}
