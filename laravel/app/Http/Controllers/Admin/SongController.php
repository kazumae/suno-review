<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewRequest;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function index(): Response
    {
        $songs = Song::withCount('reviews')->latest()->get();

        return Inertia::render('Admin/Songs/Index', [
            'songs' => $songs,
        ]);
    }

    public function create(Request $request): Response
    {
        $prefill = null;

        if ($request->filled('request_id')) {
            $rr = ReviewRequest::find($request->integer('request_id'));
            if ($rr) {
                $prefill = [
                    'request_id' => $rr->id,
                    'title' => $rr->title,
                    'suno_url' => $rr->suno_url,
                    'youtube_url' => $rr->youtube_url,
                    'genre' => $rr->genre,
                ];
            }
        }

        return Inertia::render('Admin/Songs/Create', [
            'prefill' => $prefill,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $song = new Song();
        $this->fill($song, $request, $data);
        $song->save();

        if ($request->filled('request_id')) {
            ReviewRequest::where('id', $request->integer('request_id'))
                ->update(['song_id' => $song->id, 'status' => 'done']);
        }

        return redirect()->route('admin.songs.index')->with('success', '楽曲を登録しました。');
    }

    public function edit(Song $song): Response
    {
        return Inertia::render('Admin/Songs/Edit', [
            'song' => $song,
        ]);
    }

    public function update(Request $request, Song $song): RedirectResponse
    {
        $data = $this->validateData($request);
        $this->fill($song, $request, $data);
        $song->save();

        return redirect()->route('admin.songs.index')->with('success', '楽曲を更新しました。');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'artist_name' => ['required', 'string', 'max:255'],
            'suno_url' => ['required', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'genre' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:pending,published'],
            'cover' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    private function fill(Song $song, Request $request, array $data): void
    {
        $song->title = $data['title'];
        $song->artist_name = $data['artist_name'];
        $song->suno_url = $this->normalizeSunoUrl($data['suno_url']);
        $song->youtube_url = $data['youtube_url'] ?? null;
        $song->genre = $data['genre'] ?? null;
        $song->tags = ! empty($data['tags'])
            ? collect($data['tags'])->map(fn ($t) => trim($t))->filter()->values()->all()
            : null;
        $song->description = $data['description'] ?? null;
        $song->status = $data['status'];

        if (! $song->exists) {
            $song->slug = (Str::slug($data['title']) ?: 'song').'-'.Str::lower(Str::random(6));
            $song->submitted_by = $request->user()->id;
        }

        if ($data['status'] === 'published' && ! $song->published_at) {
            $song->published_at = now();
        }

        if ($request->hasFile('cover')) {
            // デフォルトディスク（local=public / 本番=s3）に保存
            $song->cover_image_path = $request->file('cover')->store('covers');
        }
    }

    /**
     * SUNOの共有リンク(/s/{code})は埋め込み不可なので、リダイレクトを辿って
     * 正規の /song/{uuid} 形式に正規化する（/embed/{uuid} が使えるように）。
     */
    private function normalizeSunoUrl(string $url): string
    {
        if (! preg_match('#suno\.com/s/[\w-]+#', $url)) {
            return $url;
        }

        try {
            $location = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->withoutRedirecting()
                ->timeout(5)
                ->get($url)
                ->header('Location');

            if ($location) {
                $path = parse_url($location, PHP_URL_PATH);
                if ($path) {
                    return 'https://suno.com'.$path;
                }
            }
        } catch (\Throwable $e) {
            // 解決できなければ元のURLのまま（再生リンクとしては有効）
        }

        return $url;
    }
}

