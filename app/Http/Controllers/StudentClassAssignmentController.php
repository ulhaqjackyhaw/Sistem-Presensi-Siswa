<?php

namespace App\Http\Controllers;

use App\Models\StudentClassAssignment;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class StudentClassAssignmentController extends Controller
{
    public function index(Request $request)
    {
        // Implementasi index jika diperlukan
    }
public function create(Request $request)
{
    if ($request->ajax()) {
        // Ambil semua siswa, plus eager load assignment terakhir beserta schoolClass
        $students = Student::with('currentAssignment');

        return DataTables::of($students)
            ->addIndexColumn()
            // Tidak perlu menambahkan kolom dasar karena DataTables otomatis memetakan kolom yang ada
            ->editColumn('gender', fn($s) => $s->gender === 'L' ? 'Laki-laki' : 'Perempuan')
            // kolom kelas: cek currentAssignment
            ->addColumn('class_name', function($s) {
                if (! $s->currentAssignment || ! $s->currentAssignment->schoolClass) {
                    return '-';
                }
                $c = $s->currentAssignment->schoolClass;
                return "{$c->name} {$c->parallel_name}";
            })
            // Menangani pencarian custom untuk gender
            ->filterColumn('gender', function($query, $keyword) {
                if (stripos('laki', $keyword) !== false || stripos('pria', $keyword) !== false) {
                    $query->where('gender', 'L');
                } else if (stripos('perempuan', $keyword) !== false || stripos('wanita', $keyword) !== false) {
                    $query->where('gender', 'P');
                } else {
                    $query->where('gender', 'like', "%$keyword%");
                }
            })
            ->make(true);
    }

    return view('student_class_assignments.create', [
        'classes'       => SchoolClass::all(),
        'academicYears' => AcademicYear::all(),
    ]);
}
 public function createForClass(Request $request, $class_id)
{
    $selectedClass = SchoolClass::with('academicYear')->find($class_id); // Eager load academicYear

    if (!$selectedClass) {
        return redirect()->route('manage-classes.index')->with('error', 'Kelas tidak ditemukan.');
    }

    if ($request->ajax()) {
        // Menggunakan query builder untuk fleksibilitas sorting dan searching
        $query = Student::query()->with('currentAssignment.schoolClass');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('class_name', function($s) {
                if ($s->currentAssignment && $s->currentAssignment->schoolClass) {
                    $c = $s->currentAssignment->schoolClass;
                    return "{$c->name} {$c->parallel_name}";
                }
                return '-';
            })
            ->filterColumn('gender', function($query, $keyword) {
                // Pencarian khusus untuk gender (L/P menjadi Laki-laki/Perempuan)
                if (stripos('laki', $keyword) !== false || stripos('pria', $keyword) !== false) {
                    $query->where('gender', 'L');
                } else if (stripos('perempuan', $keyword) !== false || stripos('wanita', $keyword) !== false) {
                    $query->where('gender', 'P');
                } else {
                    $query->where('gender', 'like', "%$keyword%");
                }
            })
            ->editColumn('gender', function($s) {
                return $s->gender === 'L' ? 'Laki-laki' : 'Perempuan';
            })
            ->make(true);
    }

    // Menggunakan view 'student_class_assignments.create'
    // Jika nama file blade Anda adalah manage-student-class-assignments/create.blade.php,
    // maka path view-nya adalah 'manage-student-class-assignments.create'
    return view('student_class_assignments.create', [ // Pastikan path view ini benar
        'academicYears'   => AcademicYear::orderBy('start_year', 'desc')->orderBy('semester', 'desc')->get(), //
        'selectedClassId' => $class_id, //
        'selectedClass'   => $selectedClass, //
    ]);
}



    public function store(Request $request)
{
    // Tambahkan validasi di awal
    $validatedData = $request->validate([
        'nisns'             => 'required|array|min:1',
        'nisns.*'           => 'required|string', // Asumsi NISN adalah string, sesuaikan jika integer
        'class_id'          => 'required|exists:classes,id', // Pastikan class_id valid dan ada di tabel school_classes
        'academic_year_id'  => 'required|exists:academic_years,id', // Pastikan academic_year_id valid
    ]);

    $nisns = $validatedData['nisns'];
    $classId = $validatedData['class_id'];
    $yearId = $validatedData['academic_year_id'];
    $assignedBy = Auth::id();

    // Tambahan: Pastikan user yang melakukan aksi ini memiliki otorisasi (jika diperlukan)
    if (!$assignedBy) {
        return response()->json(['message' => 'Aksi tidak diizinkan. Silakan login terlebih dahulu.'], 403);
    }

    $counter = 0;
    $failedStudentsInfo = []; // Untuk menyimpan info siswa yang gagal diproses

    foreach ($nisns as $nisn) {
        $student = Student::where('nisn', $nisn)->first();

        if ($student) {
            // Logika updateOrCreate Anda sudah baik.
            // Pastikan student_id di tabel student_class_assignments memang menyimpan NISN.
            // Jika student_id seharusnya merujuk ke kolom 'id' di tabel 'students', maka gunakan $student->id.
            StudentClassAssignment::updateOrCreate(
                [
                    'student_id'       => $nisn, // Atau $student->id jika kolom ini adalah foreign key ke students.id
                    'academic_year_id' => $yearId,
                ],
                [
                    'class_id'    => $classId,
                    'assigned_by' => $assignedBy,
                ]
            );
            $counter++;
        } else {
            // Kumpulkan info siswa yang tidak ditemukan
            $failedStudentsInfo[] = $nisn;
        }
    }

    if (count($failedStudentsInfo) > 0) {
        $errorMessage = $counter . ' siswa berhasil dipindahkan. Namun, ' . count($failedStudentsInfo) .
                        ' siswa dengan NISN berikut tidak ditemukan: ' . implode(', ', $failedStudentsInfo) . '.';
        // Anda bisa memutuskan status code yang sesuai, misal 207 (Multi-Status) jika sebagian berhasil
        return response()->json(['message' => $errorMessage, 'status' => 'partial_success'], 207);
    }

    return response()->json([
        'message' => $counter . ' siswa berhasil dipindahkan.',
        'status' => 'success' // Tambahkan status untuk kemudahan di front-end jika perlu
    ]);
}

    public function getClassesByYear(Request $request)
    {
        $yearId = $request->input('year_id');
        $classes = SchoolClass::where('academic_year_id', $yearId)->get();
        return response()->json($classes);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentClassAssignment $studentClassAssignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentClassAssignment $studentClassAssignment)
    {
        //
    }
}
