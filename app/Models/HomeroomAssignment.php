<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeroomAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'academic_year_id',
    ];

    /**
     * Relasi ke tabel teachers
     */

     public function teacher()
     {
         return $this->belongsTo(Teacher::class, 'teacher_id', 'nip');
     }
     


    /**
     * Relasi ke tabel classes
     */
    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Relasi ke tabel academic_years
     */
    public function academicYear()
{
    return $this->belongsTo(AcademicYear::class, 'academic_year_id');
}
}
