<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model
{
    protected $fillable = ['title', 'start_date', 'is_active', 'allocation_status'];

    public function gaLogs()
    {
        return $this->hasMany(GaAllocationLog::class, 'exam_session_id');
    }

    // Satu sesi punya banyak ruangan
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Satu sesi punya banyak alokasi peserta (kartu ujian)
    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }
    
    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class)->orderBy('exam_date')->orderBy('start_time');
    }

    public function roomSupervisors()
    {
        return $this->hasMany(RoomSupervisor::class);
    }
}
