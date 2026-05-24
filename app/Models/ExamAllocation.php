<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAllocation extends Model
{
    protected $fillable = [
        'exam_schedule_id',
        'room_id',
        'student_id',
        'desk_number'
    ];

    public function schedule()
    {
        return $this->belongsTo(ExamSchedule::class, 'exam_schedule_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}