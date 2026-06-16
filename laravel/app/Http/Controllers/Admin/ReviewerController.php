<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewerApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewerController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        $reviewers = User::whereIn('role', ['reviewer', 'admin'])
            ->withCount('reviews')
            ->orderBy('name')
            ->get(['id', 'name', 'handle', 'email', 'role']);

        return Inertia::render('Admin/Reviewers/Index', [
            'reviewers' => $reviewers,
        ]);
    }

    public function create(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        $prefill = null;

        if ($request->filled('application_id')) {
            $application = ReviewerApplication::find($request->integer('application_id'));
            if ($application) {
                $prefill = [
                    'application_id' => $application->id,
                    'name' => $application->name,
                    'email' => $application->email,
                    'suno_id' => $application->suno_id,
                    'bio' => $application->motivation,
                ];
            }
        }

        return Inertia::render('Admin/Reviewers/Create', [
            'prefill' => $prefill,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'handle' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,handle'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'application_id' => ['nullable', 'integer', 'exists:reviewer_applications,id'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'handle' => $data['handle'],
            'email' => $data['email'],
            'password' => $data['password'],
            'bio' => $data['bio'] ?? null,
            'role' => 'reviewer',
        ]);

        if (! empty($data['application_id'])) {
            ReviewerApplication::where('id', $data['application_id'])->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'approved_user_id' => $user->id,
            ]);
        }

        return redirect()->route('admin.reviewers.index')->with('success', 'レビュワーを作成しました。');
    }
}
