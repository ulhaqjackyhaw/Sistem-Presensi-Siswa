<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hour extends Model
{
    use HasFactory;
    protected $table = 'hours';
    protected $fillable = [
        'session_type',
        'slot_number',
        'start_time',
        'end_time',
    ];

    public function classSchedules()
    {
        return $this->hasMany(ClassSchedule::class, 'hour_id');
    }
}
