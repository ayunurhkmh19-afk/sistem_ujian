<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['exam_session_id', 'master_room_id', 'name', 'capacity'];

    public function session()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }

    // Untuk menghitung berapa kursi yang sudah terisi di ruangan ini
    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }

    public function masterRoom()
    {
        return $this->belongsTo(MasterRoom::class);
    }
}