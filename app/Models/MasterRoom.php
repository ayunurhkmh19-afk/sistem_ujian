<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterRoom extends Model
{
    protected $fillable = ['name', 'capacity'];

    // Relasi untuk melihat history penggunaan (Ruangan sesi mana saja yg pakai)
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
