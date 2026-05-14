<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GaAllocationLog extends Model
{
    protected $fillable = [
        'exam_session_id',
        'total_generations',
        'best_fitness_score',
        'execution_time_seconds',
        'error_message'
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
}
