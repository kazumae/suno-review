<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Inertia\Inertia;
use Inertia\Response;

class SongController extends Controller
{
    public function show(Song $song): Response
    {
        abort_unless($song->status === 'published', 404);

        // updated_at は「コンテンツの最終編集」として sitemap の lastmod と OGP画像のバージョンに使うため、
        // 閲覧カウントでは更新しない
        $song->timestamps = false;
        $song->increment('view_count');
        $song->timestamps = true;

        $song->load([
            'submitter:id,name,handle',
            'reviews' => fn ($q) => $q->whereNotNull('published_at')
                ->with('reviewer:id,name,handle,avatar_path,bio')
                ->latest('published_at'),
        ]);

        return Inertia::render('Songs/Show', [
            'song' => $song,
        ]);
    }
}
