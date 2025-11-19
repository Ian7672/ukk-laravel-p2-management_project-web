<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blocker extends Model
{
    use HasFactory;

    protected $primaryKey = 'blocker_id';

    protected $fillable = [
        'user_id',
        'subtask_id',
        'status',
    ];

    /**
     * Relasi ke User yang meminta bantuan
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Subtask yang terblokir
     */
    public function subtask()
    {
        return $this->belongsTo(Subtask::class, 'subtask_id', 'subtask_id');
    }

    /**
     * Scope untuk blocker berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk blocker berdasarkan priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope untuk blocker yang belum resolved
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['pending', 'in_progress']);
    }
}
