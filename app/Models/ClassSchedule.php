<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use App\Models\AcademicYear;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $table = 'class_schedules';
    protected $fillable = [
        'class_id',
        'assignment_id',
        'day_of_week',
        'hour_id',
    ];


    /**
     * Relationship with Class Room model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    /**
     * Get formatted time
     *
     * @return string
     */
    public function getTimeRangeAttribute()
    {
        return date('H:i', strtotime($this->start_time)) . ' - ' . date('H:i', strtotime($this->end_time));
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
    public function hour()
    {
        return $this->belongsTo(Hour::class);
    }

    public function assignment()
    {
        return $this->belongsTo(TeachingAssignment::class, 'assignment_id');
    }
}
