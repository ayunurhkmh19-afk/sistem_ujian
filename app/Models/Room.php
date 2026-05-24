<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name', 'capacity'];

    public function examSessions()
    {
        return $this->belongsToMany(ExamSession::class, 'exam_session_room');
    }

    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }
}