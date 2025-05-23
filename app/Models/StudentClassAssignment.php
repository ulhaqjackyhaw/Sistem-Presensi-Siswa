<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentClassAssignment extends Model
{
    use HasFactory;
    protected $table = 'student_class_assignments';
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_id',
    ];
}
