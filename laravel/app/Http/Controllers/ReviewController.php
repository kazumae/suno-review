<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function show(Review $review): Response
    {
        abort_if($review->published_at === null, 404);

        $review->load([
            'song',
            'reviewer:id,name,handle,avatar_path,bio',
        ]);

        return Inertia::render('Reviews/Show', [
            'review' => $review,
        ]);
    }
}
