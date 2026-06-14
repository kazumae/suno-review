<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Song;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class FeaturedController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()->isAdmin(), 403);

        $slots = [];
        foreach (Song::whereNotNull('featured_position')->get(['id', 'featured_position']) as $s) {
            $slots[$s->featured_position] = $s->id;
        }

        return Inertia::render('Admin/Featured/Index', [
            'songs' => Song::published()->orderBy('title')->get(['id', 'title', 'artist_name']),
            'slots' => [
                1 => $slots[1] ?? null,
                2 => $slots[2] ?? null,
                3 => $slots[3] ?? null,
                4 => $slots[4] ?? null,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isAdmin(), 403);

        $slots = collect($request->input('slots', []))
            ->map(fn ($v) => ($v === '' || $v === null) ? null : (int) $v);

        $validIds = Song::whereIn('id', $slots->filter()->values())->pluck('id')->all();

        DB::transaction(function () use ($slots, $validIds) {
            Song::whereNotNull('featured_position')->update(['featured_position' => null]);

            foreach ($slots as $pos => $songId) {
                if ($songId && in_array($songId, $validIds, true)) {
                    Song::where('id', $songId)->update(['featured_position' => (int) $pos]);
                }
            }
        });

        return back()->with('success', 'メインビジュアルを更新しました。');
    }
}
