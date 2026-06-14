<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('song_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('suno_url');
            $table->string('youtube_url')->nullable();
            $table->string('title')->nullable();
            $table->string('genre')->nullable();
            $table->text('note')->nullable(); // 依頼メッセージ
            $table->string('status')->default('open'); // open|assigned|done|rejected
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_requests');
    }
};
