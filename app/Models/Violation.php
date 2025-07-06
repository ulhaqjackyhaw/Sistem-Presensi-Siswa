<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'violation_points_id', // Nama foreign key di database
        'violation_date',
        'academic_year_id',
        'description',
        'penalty',
        'reported_by',
        'evidence',
        'status',
        // Tambahan validasi
        'validation_status',
        'validator_id', // Ini akan menyimpan ID dari tabel teachers
        'validation_notes',
        'validated_at',
        'viewed_at',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'validated_at' => 'datetime',
        'viewed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'nisn');
    }

    public function violationPoint()
    {
        // Pastikan argumen kedua adalah nama foreign key di tabel 'violations'
        return $this->belongsTo(ViolationPoint::class, 'violation_points_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class); // Asumsi FK adalah academic_year_id
    }

    public function teacher() // Relasi untuk guru yang melaporkan (reported_by berisi NIP)
    {
        return $this->belongsTo(Teacher::class, 'reported_by', 'nip');
    }

    public function validator() // Relasi untuk guru yang memvalidasi (validator_id berisi NIP Teacher)
    {
        // Cocokkan validator_id (nip guru) dengan kolom nip di tabel teachers
        return $this->belongsTo(Teacher::class, 'validator_id', 'nip');
    }
}
