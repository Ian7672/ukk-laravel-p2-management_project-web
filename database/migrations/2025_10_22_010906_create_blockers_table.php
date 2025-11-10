<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('blockers', function (Blueprint $table) {
            $table->id('blocker_id');
            $table->unsignedBigInteger('user_id'); // User yang meminta bantuan
            $table->unsignedBigInteger('subtask_id'); // Subtask yang terblokir
            $table->text('description'); // Deskripsi blocker
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('assigned_to')->nullable(); // Team lead yang ditugaskan
            $table->text('solution')->nullable(); // Solusi dari team lead
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('subtask_id')->references('subtask_id')->on('subtasks')->onDelete('cascade');
            $table->foreign('assigned_to')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockers');
    }
};
