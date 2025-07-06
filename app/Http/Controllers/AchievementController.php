<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Student;
use App\Models\AchievementPoint;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\HomeroomAssignment;
use App\Models\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function __construct()
    {
        $this->middleware('homeroom.teacher')->except(['index', 'show', 'allStudents']);

        // Middleware khusus untuk allStudents yang memperbolehkan guru BK dan admin
        $this->middleware('role:Guru BK|Admin Sekolah')->only('allStudents');
    }

    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if ($teacher) {
            // Hanya tampilkan prestasi yang dilaporkan oleh guru yang login
            $achievements = Achievement::with(['student', 'achievementPoint', 'academicYear', 'teacher'])
                ->where('awarded_by', $teacher->nip)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Jika bukan guru, tidak tampilkan apapun (atau bisa diubah sesuai kebutuhan)
            $achievements = collect([]);
        }
        return view('achievements.index', compact('achievements'));
    }

    public function create()
    {
        $teacher = Auth::user()->teacher;

        // Ambil kelas yang diampu oleh guru sebagai wali kelas
        $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->first();

        if (!$homeroomClass) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        }

        // Ambil siswa dari kelas tersebut
        $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
            $query->where('class_id', $homeroomClass->class_id)
                  ->where('academic_year_id', $homeroomClass->academic_year_id);
        })->with('currentAssignment')->get();

        $achievementPoints = AchievementPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('achievements.create', compact('students', 'achievementPoints', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'achievements_name' => 'required|string|max:255',
            'achievement_points_id' => 'required|exists:achievement_points,id',
            'achievement_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat melaporkan prestasi.');
            }

            // Verifikasi bahwa siswa tersebut adalah siswa dari kelas yang diampu guru
            $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
                ->whereHas('academicYear', function($query) {
                    $query->where('is_active', true);
                })
                ->first();

            if (!$homeroomClass) {
                return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            }

            $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
                ->where('class_id', $homeroomClass->class_id)
                ->where('academic_year_id', $homeroomClass->academic_year_id)
                ->exists();

            if (!$isStudentInClass) {
                return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            }

            $achievement = new Achievement([
                'student_id' => $request->student_id,
                'achievements_name' => $request->achievements_name,
                'achievement_points_id' => $request->achievement_points_id,
                'achievement_date' => $request->achievement_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'status' => $request->status,
                'awarded_by' => $teacher->nip,
            ]);

            if ($request->hasFile('evidence')) {
                $path = $request->file('evidence')->store('achievements/evidence', 'public');
                $achievement->evidence = $path;
            }

            $achievement->save();

            DB::commit();
            return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Achievement $achievement)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        // Cek apakah user adalah pelapor
        if ($teacher && $achievement->awarded_by !== $teacher->nip) {
            abort(403, 'Anda tidak berhak mengakses detail prestasi ini.');
        }
        $achievement->load(['student', 'achievementPoint', 'academicYear', 'teacher']);
        return view('achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement)
    {
        $teacher = Auth::user()->teacher;

        // Ambil kelas yang diampu oleh guru sebagai wali kelas
        $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->first();

        if (!$homeroomClass) {
            return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        }

        // Ambil siswa dari kelas tersebut
        $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
            $query->where('class_id', $homeroomClass->class_id)
                  ->where('academic_year_id', $homeroomClass->academic_year_id);
        })->with('currentAssignment')->get();

        $achievementPoints = AchievementPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('achievements.edit', compact('achievement', 'students', 'achievementPoints', 'academicYears'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'achievements_name' => 'required|string|max:255',
            'achievement_points_id' => 'required|exists:achievement_points,id',
            'achievement_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat mengedit prestasi.');
            }

            // Verifikasi bahwa siswa tersebut adalah siswa dari kelas yang diampu guru
            $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
                ->whereHas('academicYear', function($query) {
                    $query->where('is_active', true);
                })
                ->first();

            if (!$homeroomClass) {
                return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            }

            $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
                ->where('class_id', $homeroomClass->class_id)
                ->where('academic_year_id', $homeroomClass->academic_year_id)
                ->exists();

            if (!$isStudentInClass) {
                return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            }

            $achievement->update([
                'student_id' => $request->student_id,
                'achievements_name' => $request->achievements_name,
                'achievement_points_id' => $request->achievement_points_id,
                'achievement_date' => $request->achievement_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            if ($request->hasFile('evidence')) {
                // Hapus bukti lama jika ada
                if ($achievement->evidence) {
                    Storage::disk('public')->delete($achievement->evidence);
                }
                $path = $request->file('evidence')->store('achievements/evidence', 'public');
                $achievement->evidence = $path;
                $achievement->save();
            }

            DB::commit();
            return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Achievement $achievement)
    {
        DB::beginTransaction();
        try {
            if ($achievement->evidence) {
                Storage::disk('public')->delete($achievement->evidence);
            }
            $achievement->delete();
            DB::commit();
            return redirect()->route('achievements.index')->with('success', 'Prestasi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Validasi prestasi oleh Guru BK
     */
    public function validateAchievement(Request $request, Achievement $achievement)
    {
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'nullable|string',
        ]);

        $achievement->validation_status = $request->validation_status;
        $achievement->validation_notes = $request->validation_notes;
        $achievement->validator_id = optional(Auth::user()->teacher)->nip;
        $achievement->validated_at = now();
        $achievement->save();

        return redirect()->route('achievements.index')->with('success', 'Validasi prestasi berhasil.');
    }

    /**
     * Endpoint untuk autocomplete siswa berdasarkan nama dan wali kelas
     */
    public function autocompleteSiswa(Request $request)
    {
        $term = $request->get('term', '');
        $teacher = Auth::user()->teacher;
        $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            ->whereHas('academicYear', function($query) {
                $query->where('is_active', true);
            })
            ->first();

        $students = [];
        if ($homeroomClass) {
            $query = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
                $query->where('class_id', $homeroomClass->class_id)
                      ->where('academic_year_id', $homeroomClass->academic_year_id);
            });
            if ($term !== '') {
                $query->where('name', 'like', '%' . $term . '%');
            }
            $students = $query->with('currentAssignment.schoolClass')->get();
        }

        $result = [];
        foreach ($students as $student) {
            $className = optional(optional($student->currentAssignment)->schoolClass)->name;
            $parallelName = optional(optional($student->currentAssignment)->schoolClass)->parallel_name;
            $classInfo = '';

            if ($className && $parallelName) {
                $classInfo = ' - ' . $className . ' ' . $parallelName;
            } elseif ($className) {
                $classInfo = ' - ' . $className;
            }

            $result[] = [
                'id' => $student->nisn,
                'value' => $student->name . $classInfo
            ];
        }
        return response()->json($result);
    }

    /**
     * Menampilkan daftar semua siswa dengan prestasi
     */
    public function allStudents(Request $request)
    {
        // Mendapatkan filter dari request
        $search = $request->search;
        $classFilter = $request->class;

        // Query dasar untuk mendapatkan siswa dengan prestasi dan join ke assignment & kelas
        $query = Student::select('students.*')
            ->selectRaw('COUNT(achievements.id) as achievements_count')
            ->selectRaw('SUM(achievement_points.points) as total_point')
            ->join('achievements', 'students.nisn', '=', 'achievements.student_id')
            ->join('achievement_points', 'achievements.achievement_points_id', '=', 'achievement_points.id')
            // Join ke assignment terbaru (subquery)
            ->leftJoin(DB::raw('(
                SELECT t1.* FROM student_class_assignments t1
                INNER JOIN (
                    SELECT student_id, MAX(updated_at) as max_updated
                    FROM student_class_assignments
                    GROUP BY student_id
                ) t2 ON t1.student_id = t2.student_id AND t1.updated_at = t2.max_updated
            ) as latest_assignment'), 'students.nisn', '=', 'latest_assignment.student_id')
            ->leftJoin('classes', 'latest_assignment.class_id', '=', 'classes.id')
            ->where('achievements.validation_status', 'approved')
            ->groupBy('students.nisn', 'students.name')
            ->orderByDesc('total_point');

        // Tambahkan filter pencarian jika ada
        if ($search) {
            $query->where('students.name', 'like', "%{$search}%");
        }

        // Tambahkan filter kelas jika ada
        if ($classFilter) {
            $query->where('classes.name', $classFilter);
        }

        // Dapatkan daftar kelas unik untuk dropdown filter
        $classes = DB::table('classes')->distinct()->pluck('name')->sort()->values();

        // Eksekusi query dengan paginasi
        $students = $query->paginate(15);

        return view('achievements.all_students', compact('students', 'classes'));
    }
}
