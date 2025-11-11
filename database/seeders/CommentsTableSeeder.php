<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentsTableSeeder extends Seeder
{
    public function run()
    {
        $projectComment = Comment::create([
            'project_id' => 1,
            'user_id' => 2, // team lead
            'comment_text' => 'Pastikan milestone pembayaran selesai sebelum akhir sprint.',
            'comment_type' => 'project',
        ]);

        Comment::create([
            'project_id' => 1,
            'user_id' => 3, // dev1
            'comment_text' => 'Baik, saya update estimasi dan statusnya.',
            'comment_type' => 'project',
            'parent_id' => $projectComment->comment_id,
        ]);

        $cardComment = Comment::create([
            'card_id' => 2,
            'user_id' => 3,
            'comment_text' => 'Login form sudah siap, tinggal integrasi API.',
            'comment_type' => 'card',
        ]);

        Comment::create([
            'card_id' => 2,
            'user_id' => 2,
            'comment_text' => 'Sip, jangan lupa tambahkan validasi OTP jika sempat.',
            'comment_type' => 'card',
            'parent_id' => $cardComment->comment_id,
        ]);

        Comment::create([
            'subtask_id' => 1,
            'user_id' => 5, // designer1
            'comment_text' => 'UI untuk login sudah match dengan style guide.',
            'comment_type' => 'subtask',
        ]);
    }
}
