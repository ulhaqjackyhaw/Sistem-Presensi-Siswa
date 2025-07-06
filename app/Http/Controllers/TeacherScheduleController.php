<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if (!$teacher) {
            abort(403, 'Akses hanya untuk guru.');
        }

        $academicYears = \App\Models\AcademicYear::orderByDesc('start_year')->get();
        $activeAcademicYear = $request->academic_year_id
            ? \App\Models\AcademicYear::find($request->academic_year_id)
            : \App\Models\AcademicYear::where('is_active', 1)->first();
        $activeAcademicYearId = $activeAcademicYear ? $activeAcademicYear->id : null;

        // Ambil teaching assignments guru di tahun akademik terpilih
        $teachingAssignments = \App\Models\TeachingAssignment::with(['subject', 'schoolClass'])
            ->where('teacher_id', $teacher->nip)
            ->where('academic_year_id', $activeAcademicYearId)
            ->pluck('id');

        // Ambil jadwal dari class_schedules yang assignment_id-nya milik guru tsb
        $schedules = \App\Models\ClassSchedule::with(['assignment.subject', 'schoolClass', 'hour'])
            ->whereIn('assignment_id', $teachingAssignments)
            ->orderBy('day_of_week')
            ->orderBy('hour_id')
            ->get();

        $days = [1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat'];
        $grouped = [];
        foreach ($days as $dayNum => $dayName) {
            $daySchedules = $schedules->where('day_of_week', $dayNum)->sortBy(function($item) {
                return $item->hour ? $item->hour->slot_number : 0;
            });
            $temp = [];
            foreach ($daySchedules as $item) {
                $class = $item->schoolClass->name . ($item->schoolClass->parallel_name ? ' - ' . $item->schoolClass->parallel_name : '');
                $subject = ($item->assignment && $item->assignment->subject && !empty($item->assignment->subject->subject_name)) ? $item->assignment->subject->subject_name : '-';
                $slot = $item->hour ? $item->hour->slot_number : null;
                $start_time = $item->hour ? $item->hour->start_time : null;
                $end_time = $item->hour ? $item->hour->end_time : null;
                $key = $class . '|' . $subject;
                $temp[$key][] = [
                    'slot' => $slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'item' => $item,
                ];
            }
            foreach ($temp as $key => $entries) {
                usort($entries, function($a, $b) { return $a['slot'] <=> $b['slot']; });
                $merged = [];
                $i = 0;
                while ($i < count($entries)) {
                    $start = $entries[$i]['slot'];
                    $start_time = $entries[$i]['start_time'];
                    $end = $entries[$i]['slot'];
                    $end_time = $entries[$i]['end_time'];
                    $subject = $entries[$i]['item']->assignment && $entries[$i]['item']->assignment->subject ? $entries[$i]['item']->assignment->subject->subject_name : '-';
                    $class = $entries[$i]['item']->schoolClass->name . ($entries[$i]['item']->schoolClass->parallel_name ? ' - ' . $entries[$i]['item']->schoolClass->parallel_name : '');
                    $j = $i;
                    while (
                        $j + 1 < count($entries)
                        && $entries[$j+1]['slot'] == $entries[$j]['slot'] + 1
                        && $subject == ($entries[$j+1]['item']->assignment && $entries[$j+1]['item']->assignment->subject ? $entries[$j+1]['item']->assignment->subject->subject_name : '-')
                        && $class == ($entries[$j+1]['item']->schoolClass->name . ($entries[$j+1]['item']->schoolClass->parallel_name ? ' - ' . $entries[$j+1]['item']->schoolClass->parallel_name : ''))
                    ) {
                        $end = $entries[$j+1]['slot'];
                        $end_time = $entries[$j+1]['end_time'];
                        $j++;
                    }
                    $merged[] = [
                        'day' => $dayName,
                        'time' => ($start == $end)
                            ? ($start_time . ' - ' . $end_time)
                            : ('Jam ' . $start . '-' . $end . ' (' . $start_time . ' - ' . $end_time . ')'),
                        'subject' => $subject,
                        'class' => $class,
                    ];
                    $i = $j + 1;
                }
                foreach ($merged as $m) {
                    $grouped[] = $m;
                }
            }
        }

        $weeklySchedules = $grouped;
        $teacherName = $teacher->name;
        $calendarEvents = [];
        return view('teacher-schedule.index', compact(
            'academicYears',
            'activeAcademicYearId',
            'weeklySchedules',
            'calendarEvents',
            'teacherName'
        ));
    }
}
