<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('artist_name');
            $table->string('slug');
            $table->string('suno_url');
            $table->string('youtube_url')->nullable();
            $table->string('genre')->nullable();
            $table->json('tags')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->text('description')->nullable(); // 作者の文脈
            $table->string('status')->default('pending'); // pending|published|rejected
            $table->unsignedBigInteger('view_count')->default(0);
            $table->boolean('is_featured')->default(false); // 編集部ピック
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('genre');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
