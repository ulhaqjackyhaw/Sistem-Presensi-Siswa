<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Student;
use App\Models\ViolationPoint;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\HomeroomAssignment;
use App\Models\StudentClassAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ViolationController extends Controller
{
    public function __construct()
    {
        // Ubah: Semua guru bisa create/edit, tidak hanya wali kelas
        $this->middleware('role:Guru')->only(['create', 'store', 'edit', 'update']);

        // Middleware khusus untuk allStudents yang memperbolehkan guru BK dan admin
        $this->middleware('role:Guru BK|Admin Sekolah')->only('allStudents');
    }

    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        if ($teacher) {
            // Hanya tampilkan pelanggaran yang dilaporkan oleh guru yang login
            $violations = Violation::with(['student', 'violationPoint', 'academicYear', 'teacher', 'validator'])
                ->where('reported_by', $teacher->nip)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Jika bukan guru, tidak tampilkan apapun (atau bisa diubah sesuai kebutuhan)
            $violations = collect([]);
        }
        return view('violations.index', compact('violations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teacher = Auth::user()->teacher;
        // --- KODE ASLI (hanya wali kelas) ---
        // $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
        //     ->whereHas('academicYear', function($query) {
        //         $query->where('is_active', true);
        //     })
        //     ->first();
        // if (!$homeroomClass) {
        //     return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        // }
        // $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
        //     $query->where('class_id', $homeroomClass->class_id)
        //           ->where('academic_year_id', $homeroomClass->academic_year_id);
        // })->with('currentAssignment')->get();
        // --- END KODE ASLI ---
        $students = collect([]); // atau []
        $violationPoints = ViolationPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();
        return view('violations.create', compact('students', 'violationPoints', 'academicYears'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'violation_points_id' => 'required|exists:violation_points,id',
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'penalty' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);
        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat melaporkan pelanggaran.');
            }
            // --- KODE ASLI (hanya wali kelas) ---
            // $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            //     ->whereHas('academicYear', function($query) {
            //         $query->where('is_active', true);
            //     })
            //     ->first();
            // if (!$homeroomClass) {
            //     return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            // }
            // $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
            //     ->where('class_id', $homeroomClass->class_id)
            //     ->where('academic_year_id', $homeroomClass->academic_year_id)
            //     ->exists();
            // if (!$isStudentInClass) {
            //     return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            // }
            // --- END KODE ASLI ---
            $violation = new Violation([
                'student_id' => $request->student_id,
                'violation_points_id' => $request->violation_points_id,
                'violation_date' => $request->violation_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'penalty' => $request->penalty,
                'status' => $request->status,
                'reported_by' => $teacher->nip,
            ]);
            if ($request->hasFile('evidence')) {
                $path = $request->file('evidence')->store('violations/evidence', 'public');
                $violation->evidence = $path;
            }
            $violation->save();
            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Violation $violation)
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        // Cek apakah user adalah pelapor
        if ($teacher && $violation->reported_by !== $teacher->nip) {
            abort(403, 'Anda tidak berhak mengakses detail pelanggaran ini.');
        }
        $violation->load(['student', 'violationPoint', 'academicYear', 'teacher', 'validator']); // Tambahkan eager loading validator
        return view('violations.show', compact('violation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Violation $violation)
    {
        $teacher = Auth::user()->teacher;
        // --- KODE ASLI (hanya wali kelas) ---
        // $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
        //     ->whereHas('academicYear', function($query) {
        //         $query->where('is_active', true);
        //     })
        //     ->first();
        // if (!$homeroomClass) {
        //     return redirect()->route('home')->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
        // }
        // $students = Student::whereHas('classAssignments', function($query) use ($homeroomClass) {
        //     $query->where('class_id', $homeroomClass->class_id)
        //           ->where('academic_year_id', $homeroomClass->academic_year_id);
        // })->with('currentAssignment')->get();
        // --- END KODE ASLI ---
        $students = collect([]);
        $violationPoints = ViolationPoint::all();
        $academicYears = AcademicYear::where('is_active', true)->get();
        return view('violations.edit', compact('violation', 'students', 'violationPoints', 'academicYears'));
    }

    public function update(Request $request, Violation $violation)
    {
        $request->validate([
            'student_id' => 'required|exists:students,nisn',
            'violation_points_id' => 'required|exists:violation_points,id',
            'violation_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'penalty' => 'nullable|string',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);
        DB::beginTransaction();
        try {
            $teacher = Auth::user()->teacher;
            if (!$teacher) {
                return back()->with('error', 'Hanya guru yang dapat mengedit pelanggaran.');
            }
            // --- KODE ASLI (hanya wali kelas) ---
            // $homeroomClass = HomeroomAssignment::where('teacher_id', $teacher->nip)
            //     ->whereHas('academicYear', function($query) {
            //         $query->where('is_active', true);
            //     })
            //     ->first();
            // if (!$homeroomClass) {
            //     return back()->with('error', 'Anda tidak memiliki kelas yang diampu sebagai wali kelas.');
            // }
            // $isStudentInClass = StudentClassAssignment::where('student_id', $request->student_id)
            //     ->where('class_id', $homeroomClass->class_id)
            //     ->where('academic_year_id', $homeroomClass->academic_year_id)
            //     ->exists();
            // if (!$isStudentInClass) {
            //     return back()->with('error', 'Siswa tersebut bukan dari kelas yang Anda ampu.');
            // }
            // --- END KODE ASLI ---
            $violation->update([
                'student_id' => $request->student_id,
                'violation_points_id' => $request->violation_points_id,
                'violation_date' => $request->violation_date,
                'academic_year_id' => $request->academic_year_id,
                'description' => $request->description,
                'penalty' => $request->penalty,
                'status' => $request->status,
            ]);
            if ($request->hasFile('evidence')) {
                // Hapus bukti lama jika ada
                if ($violation->evidence) {
                    Storage::disk('public')->delete($violation->evidence);
                }
                $path = $request->file('evidence')->store('violations/evidence', 'public');
                $violation->evidence = $path;
                $violation->save();
            }
            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(Violation $violation)
    {
        DB::beginTransaction();
        try {
            if ($violation->evidence) {
                Storage::disk('public')->delete($violation->evidence);
            }
            $violation->delete();
            DB::commit();
            return redirect()->route('violations.index')->with('success', 'Pelanggaran berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Endpoint untuk autocomplete kelas (semua kelas aktif)
     */
    public function autocompleteClass(Request $request)
    {
        // --- KODE ASLI (hanya kelas wali) ---
        // $user = Auth::user();
        // $teacher = $user->teacher;
        // $term = $request->input('term');
        // $query = \App\Models\HomeroomAssignment::with('class')
        //     ->where('teacher_id', $teacher->nip)
        //     ->whereHas('academicYear', function($q) {
        //         $q->where('is_active', true);
        //     });
        // if ($term) {
        //     $query->whereHas('class', function($q) use ($term) {
        //         $q->where('name', 'like', "%{$term}%");
        //     });
        // }
        // $results = $query->get();
        // $formatted = $results->map(function($item) {
        //     return [
        //         'id' => $item->class_id,
        //         'value' => $item->class->name,
        //     ];
        // });
        // return response()->json($formatted);
        // --- END KODE ASLI ---

        // Versi baru: semua kelas aktif
        $term = $request->input('term');
        $query = \App\Models\SchoolClass::where('is_active', true);
        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }
        $results = $query->get();
        $formatted = $results->map(function($item) {
            return [
                'id' => $item->id,
                'value' => $item->name . ($item->parallel_name ? ' ' . $item->parallel_name : ''),
            ];
        });
        return response()->json($formatted);
    }

    /**
     * Endpoint untuk autocomplete siswa berdasarkan kelas
     */
    public function autocompleteStudentByClass(Request $request)
    {
        $classId = $request->input('class_id');
        $term = $request->input('term');
        $query = \App\Models\Student::whereHas('classAssignments', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
        if ($term) {
            $query->where('name', 'like', "%{$term}%");
        }
        $results = $query->limit(10)->get();
        $formatted = $results->map(function($item) {
            return [
                'id' => $item->nisn,
                'value' => $item->name,
            ];
        });
        return response()->json($formatted);
    }

    /**
     * Menampilkan daftar siswa dengan pelanggaran
     */
    public function allStudents(Request $request)
    {
        // Mendapatkan filter dari request
        $search = $request->search;
        $classFilter = $request->class;

        // Query dasar untuk mendapatkan siswa dengan pelanggaran dan join ke assignment & kelas
        $query = Student::select('students.*')
            ->selectRaw('COUNT(violations.id) as violations_count')
            ->selectRaw('SUM(violation_points.points) as total_point')
            ->join('violations', 'students.nisn', '=', 'violations.student_id')
            ->join('violation_points', 'violations.violation_points_id', '=', 'violation_points.id')
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
            ->where('violations.validation_status', 'approved')
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

        return view('violations.all_students', compact('students', 'classes'));
    }
}
