<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('member')->after('email'); // admin|reviewer|member
            $table->string('handle')->nullable()->unique()->after('role'); // /reviewers/{handle}
            $table->text('bio')->nullable()->after('handle');
            $table->string('avatar_path')->nullable()->after('bio');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'handle', 'bio', 'avatar_path']);
        });
    }
};
