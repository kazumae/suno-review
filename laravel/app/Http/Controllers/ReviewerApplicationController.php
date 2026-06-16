<?php

namespace App\Http\Controllers;

use App\Models\ReviewerApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewerApplicationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'suno_id' => ['required', 'string', 'max:255'],
            'motivation' => ['required', 'string', 'max:2000'],
        ]);

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
