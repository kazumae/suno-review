<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewRequestController extends Controller
{
    public function index(): Response
    {
        $requests = ReviewRequest::with([
            'requester:id,name',
            'song:id,title',
            'assignedReviewer:id,name',
        ])->latest()->get();

        return Inertia::render('Admin/Requests/Index', [
            'requests' => $requests,
        ]);
    }

    public function update(Request $request, ReviewRequest $reviewRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,assigned,done,rejected'],
        ]);

        $reviewRequest->update($data);

        return back()->with('success', '依頼ステータスを更新しました。');
    }
}
