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
        $teamLeads = User::where('role', 'team_lead')->take(2)->get();
        
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
                        'description' => 'Saya mengalami kendala pada subtask "' . $subtask->subtask_title . '" untuk card ' . $subtask->card->card_title . '. Ada beberapa error yang tidak bisa saya selesaikan sendiri.',
                        'priority' => ['low', 'medium', 'high', 'urgent'][array_rand(['low', 'medium', 'high', 'urgent'])],
                        'status' => ['pending', 'in_progress', 'resolved'][array_rand(['pending', 'in_progress', 'resolved'])],
                        'assigned_to' => $teamLeads->count() > 0 ? $teamLeads->random()->user_id : null,
                        'solution' => $index % 2 == 0 ? 'Silakan cek dokumentasi API dan pastikan semua dependency sudah terinstall dengan benar.' : null,
                        'resolved_at' => $index % 2 == 0 ? now() : null,
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
                        'description' => 'Saya butuh bantuan pada subtask "' . $subtask->subtask_title . '" di card ' . $subtask->card->card_title . '. Ada beberapa aspek yang perlu dikonsultasikan.',
                        'priority' => ['medium', 'high'][array_rand(['medium', 'high'])],
                        'status' => ['pending', 'in_progress'][array_rand(['pending', 'in_progress'])],
                        'assigned_to' => $teamLeads->count() > 0 ? $teamLeads->random()->user_id : null,
                        'solution' => null,
                        'resolved_at' => null,
                    ]);
                }
            }
        }
    }
}
