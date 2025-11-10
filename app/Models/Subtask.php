<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $primaryKey = 'subtask_id';
    public $timestamps = false;

    protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'due_date' => 'datetime',
];

    protected $fillable = [
        'card_id','subtask_title','description','status',
        'estimated_hours','actual_hours','position','created_at'
    ];

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'subtask_id', 'subtask_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'subtask_id', 'subtask_id');
    }

    public function blockers()
    {
        return $this->hasMany(Blocker::class, 'subtask_id', 'subtask_id');
    }
}
