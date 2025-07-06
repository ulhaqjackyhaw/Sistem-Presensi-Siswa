<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index()
    {
        $attendances = StudentAttendance::with('classSchedule.assignment.subject')
            // ->forLoggedInStudent()
            ->latest('meeting_date')
            ->paginate(10);

        return view('student-attendance.index', ['attendances' => $attendances]);

    }

    
}
