<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HomeroomAssignment;

class CheckHomeroomTeacher
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->teacher) {
            return redirect()->route('home')->with('error', 'Akses ditolak. Anda bukan guru.');
        }

        $teacher = $user->teacher;

        // Cek apakah guru adalah wali kelas
        $isHomeroomTeacher = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->exists();

        if (!$isHomeroomTeacher) {
            return redirect()->route('home')->with('error', 'Akses ditolak. Anda bukan wali kelas.');
        }

        return $next($request);
    }
}
