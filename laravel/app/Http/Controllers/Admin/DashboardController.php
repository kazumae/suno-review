<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewRequest;
use App\Models\Song;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'open_requests' => ReviewRequest::where('status', 'open')->count(),
                'songs' => Song::count(),
                'published_songs' => Song::where('status', 'published')->count(),
                'reviews' => Review::count(),
            ],
            'recentRequests' => ReviewRequest::with('requester:id,name')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
