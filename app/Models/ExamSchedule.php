<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_session_id',
        'subject_name',
        'exam_date',
        'start_time',
        'end_time',
    ];

    // Casting agar tanggal otomatis jadi objek Carbon (mudah diformat di View)
    protected $casts = [
        'exam_date' => 'date',
        'start_time' => 'datetime', // atau 'string' jika ingin format H:i:s mentah
        'end_time' => 'datetime',
    ];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
}