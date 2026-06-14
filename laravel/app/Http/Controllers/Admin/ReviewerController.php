<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        return Inertia::render('Admin/Reviewers/Create');
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
        ]);

        User::create([
            'name' => $data['name'],
            'handle' => $data['handle'],
            'email' => $data['email'],
            'password' => $data['password'],
            'bio' => $data['bio'] ?? null,
            'role' => 'reviewer',
        ]);

        return redirect()->route('admin.reviewers.index')->with('success', 'レビュワーを作成しました。');
    }
}
