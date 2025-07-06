<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Achievement;
use App\Models\Violation;
use App\Imports\StudentsImport;
use App\Exports\StudentTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Validators\ValidationException;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_student')->only('index');
        $this->middleware('permission:create_student')->only('create', 'store', 'import');
        $this->middleware('permission:update_student')->only('edit', 'update');
        $this->middleware('permission:delete_student')->only('destroy');

        // Allow both GuruBK role and users with permission:read_student to access showDetail
        $this->middleware(function ($request, $next) {
            if (Auth::user()->hasRole('Guru BK') || Auth::user()->can('read_student')) {
                return $next($request);
            }
            return abort(403, 'Akses ditolak. Anda bukan wali kelas.');
        })->only('showDetail');
    }

    public function index()
    {
        if (request()->ajax()) {
            $students = Student::with(['user'])->select('students.*');

            return DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('action', function ($student) {
                    $actions = '';
                    if (Auth::check()) {
                        $actions .= "<a href='" . route('manage-students.edit', $student->nisn) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                        $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteStudent(\"{$student->nisn}\")'><i class='fas fa-trash'></i></button>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('students.index');
    }

    public function create()
    {
        $classes = SchoolClass::all();
        return view('students.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis',
            'nisn' => 'required|string|size:10|unique:students,nisn',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'required|email|unique:users,email',
            'enter_year' => 'required|digits:4',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['parent_email'],
                'password' => Hash::make("siswa123"), // Default password, $validated['nisn'] . date('dmY', strtotime($validated['birth_date']))
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Siswa');
            }

            $photoPath = null;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = $validated['nisn'] . '_' . preg_replace('/\s+/', '', $validated['name']) . '.' . $file->getClientOriginalExtension();
                $photoPath = $file->storeAs('student-photos', $fileName, 'public');
            }

            Student::create([
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'],
                'name' => $validated['name'],
                'address' => $validated['address'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'phone' => $validated['phone'],
                'parent_name' => $validated['parent_name'],
                'parent_phone' => $validated['parent_phone'],
                'parent_email' => $validated['parent_email'],
                'enter_year' => $validated['enter_year'],
                'photo' => $photoPath,
                'user_id' => $user->id,
                'is_active' => true,
            ]);

            return redirect()->route('manage-students.index')->with('success', 'Data siswa berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan siswa', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data siswa.');
        }
    }

    public function edit($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, $nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();

        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:students,nis,' . $student->nis . ',nis',
            'nisn' => 'required|string|size:10|unique:students,nisn,' . $student->nisn . ',nisn',
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'phone' => 'nullable|string|max:20',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'nullable|string|max:20',
            'parent_email' => 'required|email|unique:users,email,' . $student->user_id,
            'enter_year' => 'required|digits:4',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('student-photos', 'public');
        } else {
            $validated['photo'] = $student->photo;
        }

        $student->update([
            'nis' => $validated['nis'],
            'nisn' => $validated['nisn'],
            'name' => $validated['name'],
            'address' => $validated['address'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'],
            'phone' => $validated['phone'],
            'parent_name' => $validated['parent_name'],
            'parent_phone' => $validated['parent_phone'],
            'parent_email' => $validated['parent_email'],
            'enter_year' => $validated['enter_year'],
            'photo' => $validated['photo'],
        ]);

        $student->user->update([
            'email' => $validated['parent_email'],
            'name' => $validated['name'],
        ]);

        return redirect()->route('manage-students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();

        try {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            if ($student->user) {
                $student->user->delete();
            }

            $student->delete();

            return response()->json(['message' => 'Data siswa berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus siswa', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data siswa.'], 500);
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->route('manage-students.index')->with('success', 'Data siswa berhasil diimport.');
        } catch (ValidationException $e) {
            $errors = collect($e->failures())->map(fn($failure) => "Baris {$failure->row()}: {$failure->errors()[0]}")->implode('<br>');
            return redirect()->back()->with('error', $errors);
        } catch (\Exception $e) {
            Log::error('Gagal import data siswa', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data siswa.');
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new StudentTemplateExport, 'template_import_siswa.xlsx');
    }

    public function show($nisn)
    {
        $student = Student::where('nisn', $nisn)->firstOrFail();
        return response()->json([
            'nisn' => $student->nisn,
            'nis' => $student->nis,
            'name' => $student->name,
            'address' => $student->address,
            'phone' => $student->phone,
            'gender' => $student->gender,
            'enter_year' => $student->enter_year,
            'parent_name' => $student->parent_name,
            'parent_email' => $student->parent_email,
            'parent_phone' => $student->parent_phone,
            'birth_date' => $student->birth_date,
            'photo_url' => $student->photo ? asset('storage/' . $student->photo) : null,
        ]);
    }

    /**
     * Menampilkan detail siswa termasuk prestasi dan pelanggaran
     */
    public function showDetail(Student $student)
    {
        // Ambil data prestasi siswa
        $achievements = Achievement::where('student_id', $student->nisn)
            ->where('validation_status', 'approved')
            ->with(['achievementPoint', 'teacher', 'validator'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'ach_page');

        // Hitung total poin prestasi
        $totalAchievementPoints = Achievement::where('student_id', $student->nisn)
            ->where('validation_status', 'approved')
            ->join('achievement_points', 'achievements.achievement_points_id', '=', 'achievement_points.id')
            ->sum('achievement_points.points');

        // Ambil data pelanggaran siswa
        $violations = Violation::where('student_id', $student->nisn)
            ->where('validation_status', 'approved')
            ->with(['violationPoint', 'teacher', 'validator'])
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'vio_page');

        // Hitung total poin pelanggaran
        $totalViolationPoints = Violation::where('student_id', $student->nisn)
            ->where('validation_status', 'approved')
            ->join('violation_points', 'violations.violation_points_id', '=', 'violation_points.id')
            ->sum('violation_points.points');

        return view('students.detail', compact('student', 'achievements', 'violations', 'totalAchievementPoints', 'totalViolationPoints'));
    }
}
