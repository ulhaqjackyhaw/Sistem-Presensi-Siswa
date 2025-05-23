<?php

namespace App\Http\Controllers;

use App\Models\ClassSchedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Hour;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $search = $request->input('search', '');
    $perPage = $request->input('perPage', 10);

    $classIds = ClassSchedule::when($search, function ($query) use ($search) {
            $query->whereHas('SchoolClass', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('subject', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('teacher', fn($q) => $q->where('name', 'like', "%{$search}%"));
        })
        ->select('class_id')
        ->groupBy('class_id')
        ->pluck('class_id');

    $schedules = ClassSchedule::with(['SchoolClass'])
        ->whereIn('class_id', $classIds)
        ->groupBy('class_id') // memastikan tidak dobel
        ->selectRaw('MIN(id) as id') // ambil salah satu id jadwal tiap kelas
        ->orderByDesc('id')
        ->paginate($perPage);

    // Ambil data lengkap berdasarkan id dari hasil group
    $schedules = ClassSchedule::with(['SchoolClass', 'subject', 'teacher'])
        ->whereIn('id', $schedules->pluck('id'))
        ->paginate($perPage);

    return view('class_schedule.index', compact('schedules', 'search'));
}
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // Ambil daftar kelas, urutkan berdasarkan nama
        $classes = SchoolClass::orderBy('name')->get(); // Changed from Classes to SchoolClass

        // Ambil daftar mapel
        $subjects = Subject::orderBy('name')->get();

        // Ambil daftar jam pelajaran/istirahat
        $hours = Hour::orderBy('start_time')->get();

        // Ambil daftar pengampu (relasi guru dan mapel)
        $teachingAssignments = TeachingAssignment::with(['teacher', 'subject'])->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'teacher_id' => $item->teacher_id,
                    'teacher_name' => $item->teacher->name ?? '',
                    'subject_id' => $item->subject_id,
                    'subject_name' => $item->subject->name ?? '',
                ];
            });

        return view('class_schedule.create', compact(
            'classes',
            'subjects',
            'hours',
            'teachingAssignments'
        ));
    }


public function store(Request $request)
{
    $validated = $request->validate([
        'semester' => 'required|string',
        'class_id' => 'required|exists:classes,id',
        'schedules' => 'required|array',
        'schedules.*.*.session_type' => 'required|string|in:Jam Pelajaran,Jam Istirahat',
        'schedules.*.*.start_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.end_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.assignment_id' => 'nullable|exists:teaching_assignments,id',
    ]);

    $classId = $validated['class_id'];
    $schedules = $request->input('schedules', []);

    $dayMapping = [
        'Senin'  => 1,
        'Selasa' => 2,
        'Rabu'   => 3,
        'Kamis'  => 4,
        'Jumat'  => 5,
    ];

    DB::beginTransaction();
    try {
        foreach ($schedules as $day => $entries) {
            $dayNumber = $dayMapping[$day] ?? null;

            foreach ($entries as $entry) {
                $start = (int) $entry['start_hour_id'];
                $end   = (int) $entry['end_hour_id'];
                $sessionType = $entry['session_type'];

                // Set null jika Jam Istirahat, pastikan valid jika Jam Pelajaran
                $assignmentId = $sessionType === 'Jam Istirahat'
                    ? null
                    : ($entry['assignment_id'] ?? null);

                // Validasi manual untuk Jam Pelajaran tanpa assignment
                if ($sessionType === 'Jam Pelajaran' && !$assignmentId) {
                    throw new \Exception("Assignment tidak boleh kosong untuk sesi Jam Pelajaran pada hari $day.");
                }

                for ($hourId = $start; $hourId <= $end; $hourId++) {
                    ClassSchedule::create([
                        'class_id'      => $classId,
                        'assignment_id' => $assignmentId,
                        'day_of_week'   => $dayNumber,
                        'hour_id'       => $hourId,
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('manage-schedules.index')
            ->with('success', 'Jadwal berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage());
    }
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($class_id)
{
    $class = SchoolClass::findOrFail($class_id);

    $schedules = ClassSchedule::with(['hour', 'assignment.subject', 'assignment.teacher'])
        ->where('class_id', $class_id)
        ->get();

    return view('class_schedule.show', compact('class', 'schedules'));
}


   /**
 * Show the form for editing the specified resource.
 *
 * @param  ClassSchedule  $manageSchedule
 * @return \Illuminate\Http\Response
 */
public function edit(ClassSchedule $manageSchedule)
{
    $class_id = $manageSchedule->class_id;
    $class = SchoolClass::findOrFail($class_id);

    // Ambil semua jadwal untuk kelas ini
    $existingSchedules = ClassSchedule::with(['hour', 'assignment.subject', 'assignment.teacher'])
        ->where('class_id', $class_id)
        ->get()
        ->groupBy('day_of_week');

    // Konversi jadwal yang ada ke format yang sesuai dengan form
    $dayMap = [
        1 => 'Senin',
        2 => 'Selasa', 
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
    ];

    // Proses data jadwal untuk form
    $scheduleData = [];
    foreach ($existingSchedules as $dayNumber => $daySchedules) {
        $dayName = $dayMap[$dayNumber];
        $scheduleData[$dayName] = [];
        
        // Group consecutive hours with same assignment
        $groupedSchedules = [];
        $currentGroup = null;
        
        foreach ($daySchedules->sortBy('hour.slot_number') as $schedule) {
            $sessionType = $schedule->assignment_id ? 'Jam Pelajaran' : 'Jam Istirahat';
            $assignmentId = $schedule->assignment_id;
            
            if ($currentGroup && 
                $currentGroup['session_type'] === $sessionType && 
                $currentGroup['assignment_id'] === $assignmentId &&
                $currentGroup['end_hour_id'] + 1 === $schedule->hour_id) {
                // Extend current group
                $currentGroup['end_hour_id'] = $schedule->hour_id;
            } else {
                // Start new group
                if ($currentGroup) {
                    $groupedSchedules[] = $currentGroup;
                }
                $currentGroup = [
                    'session_type' => $sessionType,
                    'start_hour_id' => $schedule->hour_id,
                    'end_hour_id' => $schedule->hour_id,
                    'assignment_id' => $assignmentId,
                    'hour_session_type' => $schedule->hour->session_type ?? $sessionType, // tambahkan ini
                ];
            }
        }
        
        if ($currentGroup) {
            $groupedSchedules[] = $currentGroup;
        }
        
        $scheduleData[$dayName] = $groupedSchedules;
    }

    // Data untuk form (sama seperti create)
    $classes = SchoolClass::orderBy('name')->get();
    $subjects = Subject::orderBy('name')->get();
    $hours = Hour::orderBy('start_time')->get();
    
    $teachingAssignments = TeachingAssignment::with(['teacher', 'subject'])->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'teacher_id' => $item->teacher_id,
                'teacher_name' => $item->teacher->name ?? '',
                'subject_id' => $item->subject_id,
                'subject_name' => $item->subject->name ?? '',
            ];
        });

    // Default semester (bisa disesuaikan logic bisnis)
    $semester = '1'; // atau ambil dari database jika ada field semester

    return view('class_schedule.edit', compact(
        'class', 'classes', 'subjects', 'hours', 'teachingAssignments', 
        'scheduleData', 'manageSchedule', 'semester'
    ));
}

/**
 * Update the specified resource in storage.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  ClassSchedule  $manageSchedule
 * @return \Illuminate\Http\Response
 */
public function update(Request $request, ClassSchedule $manageSchedule)
{
    $class_id = $manageSchedule->class_id;

    $validated = $request->validate([
        'semester' => 'required|string',
        'class_id' => 'required|exists:classes,id',
        'schedules' => 'required|array',
        'schedules.*.*.session_type' => 'required|string|in:Jam Pelajaran,Jam Istirahat',
        'schedules.*.*.start_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.end_hour_id' => 'required|exists:hours,id',
        'schedules.*.*.assignment_id' => 'nullable|exists:teaching_assignments,id',
    ]);

    $schedules = $request->input('schedules', []);
    $dayMapping = [
        'Senin'  => 1,
        'Selasa' => 2,
        'Rabu'   => 3,
        'Kamis'  => 4,
        'Jumat'  => 5,
    ];

    DB::beginTransaction();
    try {
        // Hapus semua jadwal lama untuk kelas ini
        ClassSchedule::where('class_id', $class_id)->delete();

        // Buat jadwal baru
        foreach ($schedules as $day => $entries) {
            $dayNumber = $dayMapping[$day] ?? null;

            foreach ($entries as $entry) {
                $start = (int) $entry['start_hour_id'];
                $end   = (int) $entry['end_hour_id'];
                $sessionType = $entry['session_type'];

                $assignmentId = $sessionType === 'Jam Istirahat'
                    ? null
                    : ($entry['assignment_id'] ?? null);

                if ($sessionType === 'Jam Pelajaran' && !$assignmentId) {
                    throw new \Exception("Assignment tidak boleh kosong untuk sesi Jam Pelajaran pada hari $day.");
                }

                for ($hourId = $start; $hourId <= $end; $hourId++) {
                    ClassSchedule::create([
                        'class_id'      => $class_id,
                        'assignment_id' => $assignmentId,
                        'day_of_week'   => $dayNumber,
                        'hour_id'       => $hourId,
                    ]);
                }
            }
        }

        DB::commit();
        return redirect()->route('manage-schedules.index')->with('success', 'Jadwal berhasil diperbarui!');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $schedule = ClassSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('manage-schedules.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}