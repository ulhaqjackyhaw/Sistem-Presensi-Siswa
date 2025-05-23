<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';
    protected $fillable = [
        'class_schedule_id',
        'student_id',
        'meeting_date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'recorded_by'
    ];

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
