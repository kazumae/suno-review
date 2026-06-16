<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewerApplicationController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        $filters = [
            'status' => $request->string('status')->toString(),
            'q' => $request->string('q')->toString(),
        ];

        $applications = ReviewerApplication::query()
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['q'], fn ($query, $q) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('suno_id', 'like', "%{$q}%");
            }))
            ->latest()
            ->get();

        return Inertia::render('Admin/ReviewerApplications/Index', [
            'applications' => $applications,
            'filters' => $filters,
        ]);
    }

    public function update(Request $request, ReviewerApplication $reviewerApplication): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $reviewerApplication->update($data);

        return back()->with('success', '申込みステータスを更新しました。');
    }
}
