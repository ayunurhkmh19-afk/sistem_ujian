<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'subject_id',
        'time_session_id',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function timeSession()
    {
        return $this->belongsTo(TimeSession::class);
    }

    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }

    public function roomSupervisors()
    {
        return $this->hasMany(RoomSupervisor::class);
    }
}