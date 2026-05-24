<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
