<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\VerifyNewEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailChangeController extends Controller
{
    /**
     * 新メールアドレス宛の署名付きリンクから変更を確定する。
     * 署名・有効期限は signed ミドルウェアで検証済み。
     */
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $user = $request->user();

        // 別人のログイン状態で他人のリンクを踏むのを防ぐ
        if (! hash_equals((string) $user->getKey(), $id)) {
            abort(403);
        }

        // 保留中の変更が無ければ何もしない（リンク再利用・取消済み）
        if (blank($user->pending_email)) {
            return redirect()->route('profile.edit')
                ->with('success', 'メールアドレスは確認済みです。');
        }

        // 発行後に保留先が変わっていればリンクを無効化
        if (! hash_equals(sha1($user->pending_email), $hash)) {
            abort(403);
        }

        // 発行〜クリックの間に同じアドレスが他者に登録された場合の保険
        $takenByOther = User::where('email', $user->pending_email)
            ->whereKeyNot($user->getKey())
            ->exists();

        if ($takenByOther) {
            $user->forceFill(['pending_email' => null])->save();

            return redirect()->route('profile.edit')
                ->with('error', 'このメールアドレスは既に他のアカウントで使用されているため、変更できませんでした。');
        }

        $user->forceFill([
            'email' => $user->pending_email,
            'pending_email' => null,
            'email_verified_at' => now(),
        ])->save();

        return redirect()->route('profile.edit')
            ->with('success', 'メールアドレスを変更しました。次回から新しいメールアドレスでログインしてください。');
    }

    /**
     * 確認メールを再送する（throttle はルート側）。
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (blank($user->pending_email)) {
            return back();
        }

        $user->notify(new VerifyNewEmail($user->pending_email));

        return back()->with('success', '確認メールを再送しました。');
    }

    /**
     * 保留中のメールアドレス変更を取り消す。
     */
    public function cancel(Request $request): RedirectResponse
    {
        $request->user()->forceFill(['pending_email' => null])->save();

        return back()->with('success', 'メールアドレスの変更を取り消しました。');
    }
}
