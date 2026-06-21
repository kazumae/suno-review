<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 4軸スコアカラムとoverall_score（decimal）を削除
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn(['score_melody', 'score_lyrics', 'score_production', 'score_originality', 'overall_score']);
        });

        // overall_scoreを100点満点の整数として再追加
        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('overall_score')->nullable()->after('cover_image_path');
        });

        // 既存レビューのスコアを0にリセット
        DB::table('reviews')->update(['overall_score' => 0]);
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('overall_score');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->unsignedTinyInteger('score_melody')->nullable();
            $table->unsignedTinyInteger('score_lyrics')->nullable();
            $table->unsignedTinyInteger('score_production')->nullable();
            $table->unsignedTinyInteger('score_originality')->nullable();
            $table->decimal('overall_score', 3, 2)->nullable();
        });
    }
};
