<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class ReviewerController extends Controller
{
    public function index(): Response
    {
        $reviewers = User::query()
            ->whereIn('role', ['reviewer', 'admin'])
            ->whereNotNull('handle')
            ->whereHas('reviews', fn ($q) => $q->whereNotNull('published_at'))
            ->withCount(['reviews' => fn ($q) => $q->whereNotNull('published_at')])
            ->orderByDesc('reviews_count')
            ->orderBy('name')
            ->get();

        return Inertia::render('Reviewers/Index', [
            'reviewers' => $reviewers->map(fn (User $u) => [
                'name' => $u->name,
                'handle' => $u->handle,
                'bio' => $u->bio,
                'avatar_url' => $u->avatar_url,
                'reviews_count' => $u->reviews_count,
            ]),
        ]);
    }

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
