<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('review_requests', function (Blueprint $table) {
            // 依頼者のアーティストページ。公開時に楽曲へ引き継ぐ
            $table->string('artist_url')->nullable()->after('suno_url');
        });
    }

    public function down(): void
    {
        Schema::table('review_requests', function (Blueprint $table) {
            $table->dropColumn('artist_url');
        });
    }
};
