<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationPoint extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model ini.
     *
     * @var string
     */
    protected $table = 'violation_points';

    /**
     * Kolom-kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'violation_type',
        'violation_level',
        'points',
    ];
}
