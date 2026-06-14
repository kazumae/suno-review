<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body');
            $table->string('cover_image_path')->nullable(); // note式カバー
            $table->unsignedTinyInteger('score_melody')->nullable();
            $table->unsignedTinyInteger('score_lyrics')->nullable();
            $table->unsignedTinyInteger('score_production')->nullable();
            $table->unsignedTinyInteger('score_originality')->nullable();
            $table->decimal('overall_score', 3, 2)->nullable(); // ランキング用
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('song_id');
            $table->index('reviewer_id');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
