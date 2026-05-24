<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Level;
use App\Models\StudentClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Pastikan header di file Excel Anda adalah: 'nis', 'nama', 'kelas'
        if (empty($row['nis']) || empty($row['nama']) || empty($row['kelas'])) {
            return null;
        }

        $className = trim($row['kelas']);

        // Tentukan Level berdasarkan heuristik nama kelas
        $levelName = 'Kelas 10';
        if (preg_match('/(^11\b|\b11\b|^XI\b|\bXI\b)/i', $className)) {
            $levelName = 'Kelas 11';
        } elseif (preg_match('/(^12\b|\b12\b|^XII\b|\bXII\b)/i', $className)) {
            $levelName = 'Kelas 12';
        } elseif (preg_match('/(^10\b|\b10\b|^X\b|\bX\b)/i', $className)) {
            $levelName = 'Kelas 10';
        } else {
            // Heuristik default jika tidak cocok: coba cek karakter angka pertama
            if (strpos($className, '10') !== false) {
                $levelName = 'Kelas 10';
            } elseif (strpos($className, '11') !== false) {
                $levelName = 'Kelas 11';
            } elseif (strpos($className, '12') !== false) {
                $levelName = 'Kelas 12';
            }
        }

        // Cari atau buat Level
        $level = Level::firstOrCreate(['name' => $levelName]);

        // Cari atau buat Student Class
        $studentClass = StudentClass::firstOrCreate([
            'level_id' => $level->id,
            'name' => $className,
        ]);

        return Student::updateOrCreate(
            ['nis' => $row['nis']],
            [
                'name'  => $row['nama'], 
                'student_class_id' => $studentClass->id, 
            ]
        );
    }
}