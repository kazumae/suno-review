<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviewer_applications', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // SUNOの表示名と一致
            $table->string('email'); // uniqueにしない（却下後の再申込みを許容）
            $table->string('suno_id'); // SUNOアカウントID（@ユーザー名）
            $table->text('motivation'); // 志望動機・書きたいこと
            $table->string('status')->default('pending'); // pending|approved|rejected
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviewer_applications');
    }
};
