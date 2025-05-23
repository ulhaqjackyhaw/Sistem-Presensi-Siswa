<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use App\Models\HomeroomAssignment;
use App\Models\ClassSchedule;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'start_year',
        'end_year',
        'semester',
        'is_active',
    ];

    /**
     * Accessor untuk label tahun ajaran
     */
    public function getYearLabelAttribute()
    {
        return $this->start_year . '/' . $this->end_year . ' - Semester ' . $this->semester;
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'academic_year_id');
    }

    public function homeroomAssignments()
    {
        return $this->hasMany(HomeroomAssignment::class);
    }
    public function schedules()
    {
        return $this->hasMany(ClassSchedule::class, 'academic_year_id');
    }
}
