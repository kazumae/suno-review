<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // ログイン不要の投稿フォームは、ボットの連投に備えて IP ごとに1時間あたりの送信数を絞る
        RateLimiter::for('review-requests', fn (Request $request) => Limit::perHour(10)->by($request->ip()));
        RateLimiter::for('reviewer-applications', fn (Request $request) => Limit::perHour(5)->by($request->ip()));
    }
}
