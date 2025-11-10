<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'comment_id';
    protected $fillable = ['project_id','card_id','subtask_id','user_id','comment_text','comment_type','parent_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function subtask()
    {
        return $this->belongsTo(Subtask::class, 'subtask_id', 'subtask_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    // PERBAIKAN: Tambahkan eager loading untuk nested replies
    public function replies() {
    return $this->hasMany(Comment::class, 'parent_id', 'comment_id')
        ->with(['user', 'replies.user', 'replies.replies.user']); // Deep eager loading
}

    public function parent() {
        return $this->belongsTo(Comment::class, 'parent_id', 'comment_id');
    }
}