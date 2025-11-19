<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blocker;
use App\Models\User;
use App\Models\Subtask;

class BlockersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil beberapa user developer dan designer
        $developers = User::where('role', 'developer')->take(3)->get();
        $designers = User::where('role', 'designer')->take(2)->get();
        // Ambil beberapa subtasks beserta card terkait
        $subtasks = Subtask::with('card')->take(8)->get();
        
        if ($developers->count() > 0 && $subtasks->count() > 0) {
            // Create sample blockers for developers
            foreach ($developers as $index => $developer) {
                if ($subtasks->count() > $index) {
                    $subtask = $subtasks[$index];
                    if (!$subtask->card) {
                        continue;
                    }
                    Blocker::create([
                        'user_id' => $developer->user_id,
                        'subtask_id' => $subtask->subtask_id,
                        'status' => $index % 2 === 0 ? 'selesai' : 'pending',
                    ]);
                }
            }
        }
        
        if ($designers->count() > 0 && $subtasks->count() > 2) {
            // Create sample blockers for designers
            foreach ($designers as $index => $designer) {
                $subtaskIndex = $index + 2;
                if ($subtasks->count() > $subtaskIndex) {
                    $subtask = $subtasks[$subtaskIndex];
                    if (!$subtask->card) {
                        continue;
                    }
                    Blocker::create([
                        'user_id' => $designer->user_id,
                        'subtask_id' => $subtask->subtask_id,
                        'status' => $index % 2 === 0 ? 'selesai' : 'pending',
                    ]);
                }
            }
        }
    }
}
