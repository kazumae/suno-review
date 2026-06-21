<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $reviews = Review::with(['song:id,title', 'reviewer:id,name'])
            ->when(! $user->isAdmin(), fn ($q) => $q->where('reviewer_id', $user->id))
            ->latest()
            ->get();

        return Inertia::render('Admin/Reviews/Index', [
            'reviews' => $reviews,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('Admin/Reviews/Create', [
            'songs' => $this->songOptions(),
            'songId' => $request->integer('song_id') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $review = new Review();
        $review->reviewer_id = $request->user()->id;
        $review->slug = (Str::slug($data['title']) ?: 'review').'-'.Str::lower(Str::random(6));
        $this->fill($review, $request, $data);
        $review->save();

        $message = $review->published_at ? 'レビューを公開しました。' : 'レビューを下書き保存しました。';

        return redirect()->route('admin.reviews.index')->with('success', $message);
    }

    public function edit(Request $request, Review $review): Response
    {
        $this->authorizeReview($request, $review);

        return Inertia::render('Admin/Reviews/Edit', [
            'review' => $review,
            'songs' => $this->songOptions(),
        ]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $this->authorizeReview($request, $review);

        $data = $this->validateData($request);
        $this->fill($review, $request, $data);
        $review->save();

        $message = $review->published_at ? 'レビューを更新しました。' : 'レビューを非公開にしました。';

        return redirect()->route('admin.reviews.index')->with('success', $message);
    }

    private function songOptions()
    {
        return Song::orderBy('title')->get(['id', 'title', 'artist_name']);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'song_id' => ['required', 'exists:songs,id'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'score_melody' => ['nullable', 'integer', 'between:1,5'],
            'score_lyrics' => ['nullable', 'integer', 'between:1,5'],
            'score_production' => ['nullable', 'integer', 'between:1,5'],
            'score_originality' => ['nullable', 'integer', 'between:1,5'],
            'cover' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function fill(Review $review, Request $request, array $data): void
    {
        $review->song_id = $data['song_id'];
        $review->title = $data['title'];
        $review->body = $data['body'];

        foreach (['score_melody', 'score_lyrics', 'score_production', 'score_originality'] as $k) {
            $review->$k = $data[$k] ?? null;
        }

        $scores = array_filter(
            [$review->score_melody, $review->score_lyrics, $review->score_production, $review->score_originality],
            fn ($v) => $v !== null
        );
        $review->overall_score = count($scores) ? round(array_sum($scores) / count($scores), 2) : null;

        // 公開状態: 「公開」は既存の公開日時を保持（なければ現在時刻）、「非公開」はnull
        $review->published_at = $request->boolean('published')
            ? ($review->published_at ?? now())
            : null;

        if ($request->hasFile('cover')) {
            $review->cover_image_path = $request->file('cover')->store('covers');
        }
    }

    private function authorizeReview(Request $request, Review $review): void
    {
        $user = $request->user();
        abort_unless($user->isAdmin() || $review->reviewer_id === $user->id, 403);
    }
}
