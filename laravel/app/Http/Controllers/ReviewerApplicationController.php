<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewerApplicationStoreRequest;
use App\Models\ReviewerApplication;
use Illuminate\Http\RedirectResponse;

class ReviewerApplicationController extends Controller
{
    public function store(ReviewerApplicationStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['status'] = 'pending';

        ReviewerApplication::create($data);

        if ($request->boolean('inline')) {
            // LP埋め込みフォーム: ページ遷移せずその場に留まる（成功表示はフロント側）
            return back();
        }

        return redirect()->route('home')
            ->with('success', 'レビュワー申込みを受け付けました。編集部で確認します。');
    }
}
