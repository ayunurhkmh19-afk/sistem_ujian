<?php

namespace App\Imports;

use App\Models\Student;
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
        // Sesuaikan key array dengan header excel (lowercase)
        
        return Student::updateOrCreate(
            ['nis' => $row['nis']], // Kunci pencarian (agar tidak duplikat) [cite: 29]
            [
                'name'  => $row['nama'], 
                'class' => $row['kelas'], 
            ]
        );
    }
}