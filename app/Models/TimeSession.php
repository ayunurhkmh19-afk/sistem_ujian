<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSession extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_time', 'end_time'];

    public function examSessions()
    {
        return $this->belongsToMany(ExamSession::class, 'exam_session_time_session');
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }
}
