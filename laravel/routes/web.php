<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FeaturedController as AdminFeaturedController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ReviewerController as AdminReviewerController;
use App\Http\Controllers\Admin\ReviewRequestController as AdminReviewRequestController;
use App\Http\Controllers\Admin\SongController as AdminSongController;
use App\Http\Controllers\DevLoginController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\ReviewRequestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SongController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lp', fn () => Inertia::render('Landing'))->name('landing');

Route::get('/songs/{song}', [SongController::class, 'show'])->name('songs.show');
Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
Route::get('/reviewers/{user:handle}', [ReviewerController::class, 'show'])->name('reviewers.show');
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

// レビュー依頼フォーム（必須要件）
Route::get('/request', [ReviewRequestController::class, 'create'])->name('requests.create');
Route::post('/request', [ReviewRequestController::class, 'store'])->name('requests.store');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// バックステージ（レビュワー/管理者のみ）
Route::middleware(['auth', 'staff'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/featured', [AdminFeaturedController::class, 'index'])->name('featured.index');
    Route::put('/featured', [AdminFeaturedController::class, 'update'])->name('featured.update');
    Route::get('/requests', [AdminReviewRequestController::class, 'index'])->name('requests.index');
    Route::patch('/requests/{reviewRequest}', [AdminReviewRequestController::class, 'update'])->name('requests.update');

    Route::get('/songs', [AdminSongController::class, 'index'])->name('songs.index');
    Route::get('/songs/create', [AdminSongController::class, 'create'])->name('songs.create');
    Route::post('/songs', [AdminSongController::class, 'store'])->name('songs.store');
    Route::get('/songs/{song}/edit', [AdminSongController::class, 'edit'])->name('songs.edit');
    Route::put('/songs/{song}', [AdminSongController::class, 'update'])->name('songs.update');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/create', [AdminReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [AdminReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}/edit', [AdminReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{review}', [AdminReviewController::class, 'update'])->name('reviews.update');

    Route::get('/reviewers', [AdminReviewerController::class, 'index'])->name('reviewers.index');
    Route::get('/reviewers/create', [AdminReviewerController::class, 'create'])->name('reviewers.create');
    Route::post('/reviewers', [AdminReviewerController::class, 'store'])->name('reviewers.store');
});

if (app()->environment('local')) {
    // ローカル開発限定: ワンクリックログイン
    Route::post('/dev/login', [DevLoginController::class, 'login'])->name('dev.login');
}

require __DIR__.'/auth.php';
