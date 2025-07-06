<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $table = 'attendances'; // Tetap pakai tabel yang sama

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

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function classSchedule()
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }

    // Untuk langsung ambil nama mata pelajaran dari class_schedule
    public function subject()
    {
        return $this->hasOneThrough(
            Subject::class,
            TeachingAssignment::class,
            'id', // foreign key di TeachingAssignment (relasi dari ClassSchedule)
            'id', // foreign key di Subject
            'class_schedule_id', // foreign key di Attendance
            'subject_id' // foreign key di TeachingAssignment
        );
    }

    // Scope untuk siswa yang sedang login
    // public function scopeForLoggedInStudent($query)
    // {
    //     $studentId = auth()->user()->student->nisn;
    //     return $query->where('student_id', $studentId);
    // }
}
