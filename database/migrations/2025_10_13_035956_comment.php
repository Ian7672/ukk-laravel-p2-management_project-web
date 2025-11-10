<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
    $table->id('comment_id');

    // Relasi opsional
    $table->unsignedBigInteger('project_id')->nullable();
    $table->unsignedBigInteger('card_id')->nullable();
    $table->unsignedBigInteger('subtask_id')->nullable();

    // Untuk reply
    $table->unsignedBigInteger('parent_id')->nullable();

    // User yang berkomentar
    $table->unsignedBigInteger('user_id');

    // Isi komentar
    $table->text('comment_text');

    // Jenis komentar
    $table->enum('comment_type', ['project', 'card', 'subtask'])->default('card');

    $table->timestamps();

    // Relasi
    $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
    $table->foreign('card_id')->references('card_id')->on('cards')->onDelete('cascade');
    $table->foreign('subtask_id')->references('subtask_id')->on('subtasks')->onDelete('cascade');
    $table->foreign('parent_id')->references('comment_id')->on('comments')->onDelete('cascade');
    $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};