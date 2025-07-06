<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Student;

class AttendanceHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create_attendance_history')->only(['create', 'store']);
        $this->middleware('permission:read_attendance_history')->only(['index', 'show']);
        $this->middleware('permission:update_attendance_history')->only(['edit', 'update']);
        $this->middleware('permission:delete_attendance_history')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Data untuk dropdown
        $classes = SchoolClass::with('academicYear')->get();
        $months  = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $years = Attendance::selectRaw('YEAR(meeting_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // Ambil input filter
        $selectedClass = $request->input('kelas');
        $selectedMonth = $request->input('bulan');
        $selectedYear  = $request->input('tahun', $years->first() ?? date('Y'));

        $students   = collect();
        $presentase = [];
        $showResult = false;

        if ($selectedClass && $selectedMonth && $selectedYear) {
            $showResult = true;

            // Cari semua student_id yang punya record presensi di periode itu
            $studentIds = Attendance::whereYear('meeting_date', $selectedYear)
                ->whereMonth('meeting_date', $selectedMonth)
                ->whereHas(
                    'classSchedule',
                    fn($q) =>
                    $q->where('class_id', $selectedClass)
                )
                ->pluck('student_id')
                ->unique();

            // Ambil data siswa unik
            $students = Student::whereIn('nisn', $studentIds)->get();

            // Hitung total & hadir untuk tiap siswa
            foreach ($students as $student) {
                $baseQuery = Attendance::where('student_id', $student->nisn)
                    ->whereYear('meeting_date', $selectedYear)
                    ->whereMonth('meeting_date', $selectedMonth)
                    ->whereHas(
                        'classSchedule',
                        fn($q) =>
                        $q->where('class_id', $selectedClass)
                    );

                $total = $baseQuery->count();
                $hadir = (clone $baseQuery)
                    ->where('status', 'Hadir')
                    ->count();

                $presentase[$student->nisn] = $total > 0
                    ? round($hadir / $total * 100)
                    : 0;
            }
        }

        return view('attendances.history', compact(
            'classes',
            'months',
            'years',
            'selectedClass',
            'selectedMonth',
            'selectedYear',
            'students',
            'presentase',
            'showResult'
        ));
    }

    /**
     * Route to detail page from history page
     */
    public function redirectToDetail(Request $request)
    {
        // Redirect ke route detail dengan parameter filter yang sama
        return redirect()->route('attendances.history-detail', [
            'kelas' => $request->kelas,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }

    /**
     * Show the detail attendance recap for a class, month, year, and date.
     */
    public function detail(Request $request)
    {
        $classes = SchoolClass::with('academicYear')->get();
        $months  = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $years = Attendance::selectRaw('YEAR(meeting_date) as year')
            ->distinct()->orderByDesc('year')->pluck('year');

        $selectedClass = $request->input('kelas');
        $selectedMonth = $request->input('bulan');
        $selectedYear  = $request->input('tahun');
        $selectedDate  = $request->input('tanggal');

        $dates = collect();
        $students = collect();
        $attendances = collect();
        $showResult = false;

        if ($selectedClass && $selectedMonth && $selectedYear) {
            // Ambil semua tanggal presensi di bulan & tahun tsb
            $dates = Attendance::whereYear('meeting_date', $selectedYear)
                ->whereMonth('meeting_date', $selectedMonth)
                ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                ->orderBy('meeting_date')
                ->pluck('meeting_date')->unique();

            // Default tanggal: tanggal pertama di bulan tsb
            if (!$selectedDate && $dates->count() > 0) {
                $selectedDate = $dates->first();
            }

            if ($selectedDate) {
                $showResult = true;
                // Ambil semua siswa di kelas tsb
                $studentIds = Attendance::whereDate('meeting_date', $selectedDate)
                    ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                    ->pluck('student_id')->unique();
                $students = Student::whereIn('nisn', $studentIds)->get();
                // Ambil data presensi siswa pada tanggal tsb
                $attendances = Attendance::whereDate('meeting_date', $selectedDate)
                    ->whereHas('classSchedule', fn($q) => $q->where('class_id', $selectedClass))
                    ->get()->keyBy('student_id');
            }
        }

        return view('attendances.history-detail', compact(
            'classes',
            'months',
            'years',
            'selectedClass',
            'selectedMonth',
            'selectedYear',
            'selectedDate',
            'dates',
            'students',
            'attendances',
            'showResult'
        ));
    }
}
