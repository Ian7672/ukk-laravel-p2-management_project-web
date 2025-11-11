<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProjectMembersTableSeeder extends Seeder
{
    public function run()
    {
        $members = [
            // Project 1: Website E-commerce
            ['project_id' => 1, 'user_id' => 2, 'role' => 'admin'],   // teamlead1
            ['project_id' => 1, 'user_id' => 3, 'role' => 'member'],  // dev1
            ['project_id' => 1, 'user_id' => 5, 'role' => 'member'],  // designer1
            
            // Project 2: Aplikasi Mobile
            ['project_id' => 2, 'user_id' => 2, 'role' => 'admin'],   // teamlead1
            ['project_id' => 2, 'user_id' => 4, 'role' => 'member'],  // dev2
            ['project_id' => 2, 'user_id' => 8, 'role' => 'member'],  // designer2
            
            // Project 3: Sistem Inventory
            ['project_id' => 3, 'user_id' => 6, 'role' => 'admin'],   // teamlead2
            ['project_id' => 3, 'user_id' => 3, 'role' => 'member'],  // dev1
            ['project_id' => 3, 'user_id' => 4, 'role' => 'member'],  // dev2
            ['project_id' => 3, 'user_id' => 7, 'role' => 'member'],  // dev3
        ];

        DB::table('project_members')->insert($members);

        $workingUserIds = collect($members)
            ->pluck('user_id')
            ->unique()
            ->values();

        User::whereIn('user_id', $workingUserIds)
            ->whereIn('role', ['developer', 'designer', 'team_lead'])
            ->update(['current_task_status' => 'working']);
    }
}
