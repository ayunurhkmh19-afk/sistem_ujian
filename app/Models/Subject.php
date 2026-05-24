<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['level_id', 'name'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function examSessions()
    {
        return $this->belongsToMany(ExamSession::class, 'exam_session_subject');
    }
}
