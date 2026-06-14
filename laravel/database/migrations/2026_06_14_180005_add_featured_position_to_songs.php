<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            // メインビジュアル(モザイク)のスロット 1-4。null=未掲載
            $table->unsignedTinyInteger('featured_position')->nullable()->after('is_featured')->index();
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn('featured_position');
        });
    }
};
