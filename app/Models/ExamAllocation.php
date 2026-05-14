<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamAllocation extends Model
{
    protected $fillable = [
        'exam_session_id',
        'room_id',
        'student_id',
        'desk_number' // Diisi otomatis saat generate [cite: 30]
    ];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
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