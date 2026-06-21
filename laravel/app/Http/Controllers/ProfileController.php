<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Notifications\VerifyNewEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit');
    }

    /**
     * Update the user's profile information.
     *
     * 名前は即時更新。メールアドレスは保留方式: emailは切り替えず pending_email に保存し、
     * 新アドレス宛の確認リンクを踏んで初めて確定する。
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];

        $newEmail = $validated['email'];
        $emailChanged = $newEmail !== $user->email;

        if ($emailChanged) {
            $user->pending_email = $newEmail;
        } elseif (filled($user->pending_email)) {
            // メールアドレスを現在の値に戻した場合は保留中の変更を取り消す
            $user->pending_email = null;
        }

        $user->save();

        if ($emailChanged) {
            $user->notify(new VerifyNewEmail($newEmail));

            return Redirect::route('profile.edit')
                ->with('success', $newEmail.' に確認メールを送信しました。記載のリンクを開くとメールアドレスの変更が完了します。');
        }

        return Redirect::route('profile.edit')
            ->with('success', 'プロフィールを更新しました。');
    }
}
