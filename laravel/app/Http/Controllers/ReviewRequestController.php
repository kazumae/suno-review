<?php

namespace App\Http\Controllers;

use App\Models\ReviewRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewRequestController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Requests/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'suno_url' => ['required', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'genre' => ['nullable', 'string', 'max:50'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['requester_id'] = $request->user()?->id;
        $data['status'] = 'open';

        ReviewRequest::create($data);

        return redirect()->route('home')
            ->with('success', 'レビュー依頼を受け付けました。編集部とレビュワーが確認します。');
    }
}
