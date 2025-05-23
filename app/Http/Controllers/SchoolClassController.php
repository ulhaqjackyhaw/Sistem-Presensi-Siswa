<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\HomeroomAssignment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class SchoolClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $classes = SchoolClass::with('academicYear', 'homeroomTeacher.teacher')->select('classes.*');

            return DataTables::of($classes)
                ->addIndexColumn()
                ->addColumn('academic_year', function ($class) {
                    if ($class->academicYear) {
                        $semesterText = $class->academicYear->semester == 0 ? 'Ganjil' : 'Genap';
                        return $class->academicYear->start_year . ' - ' . $class->academicYear->end_year . ' ' . $semesterText;
                    }
                    return '-';
                })
                ->addColumn('status', function ($class) {
                    return $class->is_active ? 'Aktif' : 'Tidak Aktif';
                })
                ->addColumn('homeroom_teacher', function ($class) {
                    if ($class->homeroomTeacher && $class->homeroomTeacher->teacher) {
                        return $class->homeroomTeacher->teacher->name;
                    }
                    return '-';
                })

                ->addColumn('action', function ($class) {
                    $editUrl = route('manage-classes.edit', $class->id);
                    return <<<HTML
                        <div class="btn-group">
                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{$editUrl}">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteClass({$class->id})">
                                    <i class="fas fa-trash-alt me-1"></i>Delete
                                </a>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#assignHomeroomModal"
                                    data-class-id="{$class->id}" title="Pilih Wali Kelas">
                                <i class="fas fa-user-check"></i>
                            </button>
                        </div>
                    HTML;
                })


                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        // ✅ Tambahkan baris ini jika kamu ingin data guru langsung dimuat ke view (tanpa AJAX)
        $teachers = Teacher::all(); // atau User::where('role', 'guru')->get();
        $classes = SchoolClass::with('homeroomTeacher')->get(); // Pastikan relasi `homeroomTeacher` ada di model

        return view('manage-classes.index', compact('classes', 'teachers'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Mengambil semua tahun akademik untuk dropdown
        $academicYears = AcademicYear::all();

        // Menampilkan form tambah kelas
        return view('manage-classes.create', compact('academicYears'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parallel_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'required|in:0,1',
        ]);

        // Menyimpan data kelas
        SchoolClass::create([
            'name' => $request->name,
            'parallel_name' => $request->parallel_name,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => $request->is_active,   // Sesuaikan dengan kebutuhan
        ]);

        return redirect()->route('manage-classes.index')->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SchoolClass $schoolClass)
    {
        //
    }
    public function assignHomeroom(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'required|exists:teachers,nip',
        ]);

        $class = SchoolClass::findOrFail($request->class_id);

        // Ambil tahun akademik dari relasi kelas
        $academicYearId = $class->academic_year_id;

        HomeroomAssignment::updateOrCreate(
            ['class_id' => $request->class_id],
            [
                'teacher_id' => (string) $request->teacher_id,
                'academic_year_id' => $academicYearId
            ]
        );

        return redirect()->back()->with('success', 'Wali kelas berhasil ditetapkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $class = SchoolClass::findOrFail($id);
        $academicYears = AcademicYear::all();
        return view('manage-classes.edit', compact('class', 'academicYears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parallel_name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'is_active' => 'required|boolean',
        ]);

        $class = SchoolClass::findOrFail($id);

        $class->update([
            'name' => $request->name,
            'parallel_name' => $request->parallel_name,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('manage-classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $class = SchoolClass::findOrFail($id);

        // Jika ingin juga mengecek relasi atau kondisi lain, bisa ditambahkan di sini

        $class->delete();

        return response()->json([
            'message' => 'Data kelas berhasil dihapus.'
        ], 200);
    }
}