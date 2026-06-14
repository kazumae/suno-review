<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * レビュワー or 管理者のみ許可（招待制のバックステージ）。
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isReviewer()) {
            abort(403);
        }

        return $next($request);
    }
}
