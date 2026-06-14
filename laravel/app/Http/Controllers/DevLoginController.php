<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevLoginController extends Controller
{
    /**
     * ローカル開発限定のワンクリックログイン。
     * ルート登録時とここで二重に local 環境をガードする。
     */
    public function login(Request $request): RedirectResponse
    {
        abort_unless(app()->environment('local'), 404);

        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}
