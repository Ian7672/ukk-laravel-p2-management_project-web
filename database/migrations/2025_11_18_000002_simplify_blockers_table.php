<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('blockers')) {
            if (Schema::hasColumn('blockers', 'assigned_to')) {
                Schema::table('blockers', function (Blueprint $table) {
                    $table->dropForeign(['assigned_to']);
                });
            }

            $columnsToDrop = [
                'description',
                'priority',
                'assigned_to',
                'solution',
                'resolved_at',
                'rejected_at',
            ];

            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('blockers', $column)) {
                    Schema::table('blockers', function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }

            DB::statement("ALTER TABLE blockers MODIFY status ENUM('pending','selesai') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('blockers')) {
            return;
        }

        Schema::table('blockers', function (Blueprint $table) {
            if (!Schema::hasColumn('blockers', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('blockers', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            }

            if (!Schema::hasColumn('blockers', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable();
            }

            if (!Schema::hasColumn('blockers', 'solution')) {
                $table->text('solution')->nullable();
            }

            if (!Schema::hasColumn('blockers', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }

            if (!Schema::hasColumn('blockers', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
        });

        Schema::table('blockers', function (Blueprint $table) {
            if (Schema::hasColumn('blockers', 'assigned_to')) {
                $table->foreign('assigned_to')->references('user_id')->on('users')->onDelete('set null');
            }
        });

        DB::statement("ALTER TABLE blockers MODIFY status ENUM('pending','in_progress','resolved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
