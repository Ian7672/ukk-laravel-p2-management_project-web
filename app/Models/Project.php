<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';
    
    public $timestamps = true;

    protected $fillable = [
        'project_name',
        'description',
        'deadline',
        'status',
        'created_by'
    ];

    // ✅ Tambahkan ini untuk casting otomatis
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deadline'   => 'datetime',
    ];

    protected $attributes = [
        'status' => 'proses',
    ];

    // ✅ Relasi ke Board
    public function boards()
    {
        return $this->hasMany(Board::class, 'project_id', 'project_id');
    }

    // ✅ Relasi ke Member
    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id', 'project_id');
    }

        public function comments()
    {
        return $this->hasMany(Comment::class, 'project_id', 'project_id');
    }
}
