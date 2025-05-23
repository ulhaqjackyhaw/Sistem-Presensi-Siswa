<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'nip',
        'name',
        'phone',
        'address',
        'gender',
        'birth_date',
        'photo',
        'user_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Get the user that owns the teacher.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function homeroomAssignments()
{
    return $this->hasMany(HomeroomAssignment::class, 'teacher_id', 'nip');
}
}
