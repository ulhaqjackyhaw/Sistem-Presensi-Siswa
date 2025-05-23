<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'achievements_name',
        'achievement_points_id',
        'achievement_date',
        'academic_year_id',
        'description',
        'awarded_by',
        'evidence',
        'status'
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function achievementPoint()
    {
        return $this->belongsTo(AchievementPoint::class, 'achievement_points_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'awarded_by');
    }
}
