<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */


    public function index()
    {
        if (Auth::user()->hasRole('Admin Sekolah')) {

            // Total siswa aktif
            $activeStudents = Student::where('is_active', true)->count();

            // Total Hadir
            $present = Attendance::where('status', 'Hadir')->count();

            // Total Alpha
            $absent = Attendance::where('status', 'Alpha')->count();

            // Total Izin
            $excused = Attendance::where('status', 'Izin')->count();

            // Chart: jumlah hadir per kelas
            $chart = Attendance::where('status', 'Hadir')
                ->with('classSchedule.schoolClass') // load hingga ke kelas
                ->get()
                ->groupBy(function ($attendance) {
                    return optional($attendance->classSchedule->schoolClass)->name;
                })
                ->map(function ($attendances, $className) {
                    return [
                        'class' => $className ?? 'Tidak diketahui',
                        'total_present' => $attendances->count()
                    ];
                })
                ->values();



            return view('home', compact('activeStudents', 'present', 'absent', 'excused', 'chart'));
        } else if (Auth::user()->hasRole('Guru BK')) {
            // Top 3 siswa dengan poin prestasi tertinggi
            $topAchievementStudents = \App\Models\Student::select('students.nisn', 'students.name')
                ->leftJoin('student_class_assignments as sca', 'sca.student_id', '=', 'students.nisn')
                ->leftJoin('classes as sc', 'sca.class_id', '=', 'sc.id')
                ->leftJoin('achievements', function($join) {
                    $join->on('achievements.student_id', '=', 'students.nisn')
                        ->where('achievements.validation_status', 'approved');
                })
                ->leftJoin('achievement_points as ap', 'achievements.achievement_points_id', '=', 'ap.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(ap.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    $kelas = $item->class_name ? ($item->class_name . ($item->parallel_name ? ' - ' . $item->parallel_name : '')) : '-';
                    return (object)[
                        'name' => $item->name,
                        'class_name' => $kelas,
                        'total_point' => $item->total_point
                    ];
                });

            // Top 3 siswa dengan poin pelanggaran tertinggi
            $topViolationStudents = \App\Models\Student::select('students.nisn', 'students.name')
                ->leftJoin('student_class_assignments as sca', 'sca.student_id', '=', 'students.nisn')
                ->leftJoin('classes as sc', 'sca.class_id', '=', 'sc.id')
                ->leftJoin('violations', function($join) {
                    $join->on('violations.student_id', '=', 'students.nisn')
                        ->where('violations.validation_status', 'approved');
                })
                ->leftJoin('violation_points as vp', 'violations.violation_points_id', '=', 'vp.id')
                ->groupBy('students.nisn', 'students.name', 'sc.name', 'sc.parallel_name')
                ->selectRaw('COALESCE(SUM(vp.points),0) as total_point, sc.name as class_name, sc.parallel_name')
                ->orderByDesc('total_point')
                ->limit(3)
                ->get()
                ->map(function($item) {
                    $kelas = $item->class_name ? ($item->class_name . ($item->parallel_name ? ' - ' . $item->parallel_name : '')) : '-';
                    return (object)[
                        'name' => $item->name,
                        'class_name' => $kelas,
                        'total_point' => $item->total_point
                    ];
                });

            return view('home', compact('topAchievementStudents', 'topViolationStudents'));
        } else if (Auth::user()->hasRole('Siswa')) {
            $user = Auth::user();
            $student = Student::where('user_id', $user->id)->first();

            // Prestasi siswa (hanya yang sudah divalidasi)
            $achievements = $student->achievements()->where('validation_status', 'approved')->get();
            $prestasiTotal = $achievements->sum(function($a) {
                return optional($a->achievementPoint)->points ?? 0;
            });

            // Pelanggaran siswa (hanya yang sudah divalidasi)
            $violations = $student->violations()->where('validation_status', 'approved')->get();
            $pelanggaranTotal = $violations->sum(function($v) {
                return optional($v->violationPoint)->points ?? 0;
            });

            // Pie chart data
            $pieData = [
                'prestasi' => $prestasiTotal,
                'pelanggaran' => $pelanggaranTotal
            ];

            // Detail list prestasi
            $prestasiList = $achievements->map(function($a) {
                return [
                    'name' => $a->achievements_name,
                    'point' => optional($a->achievementPoint)->points ?? 0
                ];
            });
            // Detail list pelanggaran
            $pelanggaranList = $violations->map(function($v) {
                return [
                    'name' => optional($v->violationPoint)->violation_type ?? '-',
                    'point' => optional($v->violationPoint)->points ?? 0
                ];
            });

            // Performa kehadiran (contoh: per bulan, bisa disesuaikan)
            $attendance = \App\Models\Attendance::where('student_id', $student->nisn)
                ->selectRaw('MONTH(meeting_date) as month, COUNT(*) as total')
                ->groupBy('month')->orderBy('month')->pluck('total', 'month');
            // Dummy data jika belum ada data kehadiran
            $attendance = $attendance->isEmpty() ? collect([1=>10,2=>12,3=>8,4=>15,5=>9,6=>11,7=>13,8=>10,9=>14,10=>12,11=>13,12=>9]) : $attendance;

            return view('home', compact('pieData', 'prestasiList', 'pelanggaranList', 'attendance'));
        } else {
            return view('home');
        }
    }

    /**
     * Poin Prestasi untuk siswa (role Siswa)
     */
    public function studentAchievements(Request $request)
    {
        $user = Auth::user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $date = $request->input('date');
        $query = $student->achievements()->with('achievementPoint')->where('validation_status', 'approved');
        if ($date) {
            $query->whereDate('achievement_date', $date);
        }
        $achievements = $query->orderBy('achievement_date', 'desc')->get();
        $totalPoint = $achievements->sum(function($a) { return optional($a->achievementPoint)->points ?? 0; });
        return view('student.achievements', compact('achievements', 'totalPoint', 'date', 'student'));
    }

    /**
     * Poin Pelanggaran untuk siswa (role Siswa)
     */
    public function studentViolations(Request $request)
    {
        $user = Auth::user();
        $student = \App\Models\Student::where('user_id', $user->id)->firstOrFail();
        $date = $request->input('date');
        $query = $student->violations()->with('violationPoint')->where('validation_status', 'approved');
        if ($date) {
            $query->whereDate('violation_date', $date);
        }
        $violations = $query->orderBy('violation_date', 'desc')->get();
        $totalPoint = $violations->sum(function($v) { return optional($v->violationPoint)->points ?? 0; });
        return view('student.violations', compact('violations', 'totalPoint', 'date', 'student'));
    }
}
