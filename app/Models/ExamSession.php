<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = ['title', 'start_date', 'end_date', 'is_active', 'allocation_status'];

    public function gaLogs()
    {
        return $this->hasMany(GaAllocationLog::class, 'exam_session_id');
    }

    // Satu sesi punya banyak ruangan (via pivot di V2)
    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'exam_session_room');
    }

    // Satu sesi punya banyak mata pelajaran (via pivot di V2)
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_session_subject');
    }

    // Satu sesi punya banyak sesi waktu (via pivot di V2)
    public function timeSessions()
    {
        return $this->belongsToMany(TimeSession::class, 'exam_session_time_session');
    }

    // Satu sesi punya banyak jadwal
    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    // Satu sesi punya banyak alokasi peserta (kartu ujian) via schedules
    public function allocations()
    {
        return $this->hasManyThrough(ExamAllocation::class, ExamSchedule::class);
    }
    
    // Satu sesi punya banyak pengawas ruangan via schedules
    public function roomSupervisors()
    {
        return $this->hasManyThrough(RoomSupervisor::class, ExamSchedule::class);
    }
}
