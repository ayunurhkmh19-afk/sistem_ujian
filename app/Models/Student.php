<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'student_class_id',
    ];

    public function studentClass()
    {
        return $this->belongsTo(StudentClass::class);
    }

    public function getLevelAttribute()
    {
        return $this->studentClass ? $this->studentClass->level : null;
    }

    /**
     * Relasi untuk mengecek histori alokasi ujian siswa.
     */
    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }
}