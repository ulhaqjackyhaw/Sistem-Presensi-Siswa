<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'name',
        'parallel_name',
        'teacher_id',
        'academic_year_id',
        'is_active',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function homeroomAssignments()
    {
        return $this->hasMany(HomeroomAssignment::class, 'class_id');
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'nip');
    }

    public function homeroomTeacher()
    {
        return $this->hasOne(HomeroomAssignment::class, 'class_id')->with('teacher');
    }


    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }
}
