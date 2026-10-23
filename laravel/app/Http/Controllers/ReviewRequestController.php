<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequestStoreRequest;
use App\Models\ReviewRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ReviewRequestController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Requests/Create');
    }

    public function store(ReviewRequestStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['requester_id'] = $request->user()?->id;
        $data['status'] = 'open';

        ReviewRequest::create($data);

        if ($request->boolean('inline')) {
            // LP埋め込みフォーム: ページ遷移せずその場に留まる（成功表示はフロント側）
            return back();
        }

        return redirect()->route('home')
            ->with('success', 'レビュー依頼を受け付けました。編集部とレビュワーが確認します。');
    }
}
