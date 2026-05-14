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
        'class', // Field ini vital untuk filter range siswa
    ];

    /**
     * Relasi untuk mengecek histori alokasi ujian siswa.
     * Digunakan di Wizard Step 3 untuk memfilter siswa yang belum dapat ruangan.
     */
    public function allocations()
    {
        return $this->hasMany(ExamAllocation::class);
    }
}