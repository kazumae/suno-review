<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ReviewerController extends Controller
{
    public function show(User $user): Response
    {
        abort_unless($user->isReviewer(), 404);

        $reviews = $user->reviews()
            ->whereNotNull('published_at')
            ->with('song:id,title,slug,artist_name,cover_image_path,genre')
            ->latest('published_at')
            ->get();

        return Inertia::render('Reviewers/Show', [
            'reviewer' => $user->only(['id', 'name', 'handle', 'bio', 'avatar_url', 'suno_url']),
            'reviews' => $reviews,
        ]);
    }
}
