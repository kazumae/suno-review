<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // アーティストのトップページ(SUNOプロフィール等)。楽曲詳細でリンク表示
            $table->string('artist_url')->nullable()->after('artist_name');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('artist_url');
        });
    }
};
