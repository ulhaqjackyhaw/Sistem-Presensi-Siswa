<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'nisn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nis',
        'nisn',
        'enter_year',
        'user_id',
        'name',
        'gender',
        'birth_date',
        'address',
        'phone',
        'parent_name',
        'parent_phone',
        'parent_email',
        'photo',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Override agar route model binding pakai 'nisn' sebagai key.
     */
    public function getRouteKeyName()
    {
        return 'nisn';
    }
}
