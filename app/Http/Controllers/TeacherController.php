<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use App\Imports\TeachersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Traits\HasPermissions;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\TeacherTemplateExport;
use Maatwebsite\Excel\Validators\ValidationException;

class TeacherController extends Controller
{
    use HasPermissions;

    public function __construct()
    {
        $this->middleware('permission:read_teacher')->only('index');
        $this->middleware('permission:create_teacher')->only('create', 'store');
        $this->middleware('permission:update_teacher')->only('edit', 'update');
        $this->middleware('permission:delete_teacher')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $teachers = Teacher::query()
                ->join('users', 'teachers.user_id', '=', 'users.id')
                ->select('teachers.*', 'users.email');

            return DataTables::of($teachers)
                ->addIndexColumn()
                ->addColumn('dapodik_number', function ($teacher) {
                    return $teacher->dapodik_number ?? '-';
                })
                ->addColumn('action', function ($teacher) {
                    $id = $teacher->nip;
                    $editUrl = route('manage-teachers.edit', $id);
                    $deleteForm = '<form id="delete-form-' . $id . '" action="' . route('manage-teachers.destroy', $id) . '" method="POST" style="display: none;">'
                        . csrf_field() . method_field('DELETE') . '</form>';
                    return '
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="' . $editUrl . '">Edit</a>
                                <button type="button" class="dropdown-item text-primary" onclick="jadikanGuruBk(\'' . $id . '\')">Jadikan Guru BK</button>
                                <button type="button" class="dropdown-item text-danger" onclick="confirmDelete(\'' . $id . '\')">Hapus</button>
                            </div>
                        </div>' . $deleteForm;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('teachers.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|unique:teachers,nip|digits:18',
            'dapodik_number' => 'nullable|string|max:16|unique:teachers,dapodik_number',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required',
            'address' => 'required',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Create user account
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->nip . date('dmY', strtotime($request->birth_date))), // Default password
        ]);
        $user->assignRole('Guru');

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teacher-photos', 'public');
        }

        // Create teacher record
        Teacher::create([
            'nip' => $request->nip,
            'dapodik_number' => $request->dapodik_number ? substr($request->dapodik_number, 0, 16) : null,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
            'photo' => $photoPath,
            'user_id' => $user->id,
        ]);

        return redirect()->route('manage-teachers.index')
            ->with('success', 'Data guru berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nip)
    {
        try {
            $teacher = Teacher::with(['user', 'teachingAssignments.subject', 'teachingAssignments.schoolClass'])->where('nip', $nip)->firstOrFail();

            // Jika request AJAX, kembalikan JSON
            if (request()->ajax()) {
                // Ambil mata pelajaran yang diampu
                $subjects = $teacher->teachingAssignments->map(function ($assignment) {
                    return [
                        'subject' => $assignment->subject ? $assignment->subject->name : '-',
                        'class' => $assignment->schoolClass ?
                            ($assignment->schoolClass->name . ($assignment->schoolClass->parallel_name ? ' - ' . $assignment->schoolClass->parallel_name : ''))
                            : '-'
                    ];
                });

                // Buat status berdasarkan mata pelajaran
                $status = 'Guru';
                if ($teacher->user->hasRole('Guru BK')) {
                    $status = 'Guru BK';
                } else if ($subjects->isNotEmpty()) {
                    $status = 'Guru ' . $subjects->pluck('subject')->unique()->join(', ');
                }

                return response()->json([
                    'nip' => $teacher->nip,
                    'dapodik_number' => $teacher->dapodik_number,
                    'name' => $teacher->name,
                    'email' => $teacher->user->email,
                    'phone' => $teacher->phone,
                    'address' => $teacher->address,
                    'gender' => $teacher->gender,
                    'birth_date' => $teacher->birth_date,
                    'photo_url' => $teacher->photo ? asset('storage/' . $teacher->photo) : null,
                    'status' => $status,
                    // 'subjects' => $subjects
                ]);
            }

            return view('teachers.show', compact('teacher'));
        } catch (\Exception $e) {
            Log::error('Error in TeacherController@show: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'error' => 'Tidak dapat mengambil data guru'
                ], 500);
            }

            return redirect()->route('manage-teachers.index')
                ->with('error', 'Tidak dapat mengambil data guru');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();
        return view('teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();

        $request->validate([
            'nip' => 'required|digits:18|unique:teachers,nip,' . $nip . ',nip',
            'dapodik_number' => 'nullable|string|max:16|unique:teachers,dapodik_number,' . $nip . ',nip',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $teacher->user->id,
            'phone' => 'required',
            'address' => 'required',
            'gender' => 'required|in:L,P',
            'birth_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $photoPath = $request->file('photo')->store('teacher-photos', 'public');
            $teacher->photo = $photoPath;
        }

        // Update teacher data
        $teacher->update([
            'nip' => $request->nip,
            'name' => $request->name,
            'dapodik_number' => $request->dapodik_number ? substr($request->dapodik_number, 0, 16) : null,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender,
            'birth_date' => $request->birth_date,
        ]);

        // Update user data
        $teacher->user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return redirect()->route('manage-teachers.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();

        // Delete photo if exists
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // Delete associated user
        $teacher->user->delete();

        // Delete teacher
        $teacher->delete();

        return redirect()->route('manage-teachers.index')
            ->with('success', 'Data guru berhasil dihapus');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));

            return redirect()->route('manage-teachers.index')
                ->with('success', 'Data guru berhasil diimport');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errors = [];

            foreach ($failures as $failure) {
                $errors[] = "Baris ke-{$failure->row()}: {$failure->errors()[0]}";
            }

            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $errors));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat import data');
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new TeacherTemplateExport, 'template_import_guru.xlsx');
    }

    /**
     * Jadikan Guru BK
     */
    public function jadikanGuruBk(Request $request, $nip)
    {
        $teacher = Teacher::with('user')->where('nip', $nip)->firstOrFail();
        $user = $teacher->user;
        if ($user->hasRole('Guru BK')) {
            return response()->json(['status' => false, 'message' => 'Guru sudah menjadi Guru BK']);
        }
        $user->assignRole('Guru BK');
        return response()->json(['status' => true, 'message' => 'Guru berhasil dijadikan Guru BK']);
    }
}
