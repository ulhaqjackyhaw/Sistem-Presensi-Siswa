<?php

namespace App\Http\Controllers;

use App\Models\Violation;
use App\Models\Teacher; // Pastikan model Teacher diimport jika belum
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ViolationValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Guru BK');
    }

    public function index(Request $request)
    {
        $query = Violation::with(['student', 'violationPoint', 'teacher']);

        // Filter status: tampilkan semua, atau filter jika ada request status
        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('validation_status', 'pending');
            } elseif ($request->status === 'approved') {
                $query->where('validation_status', 'approved');
            } elseif ($request->status === 'rejected') {
                $query->where('validation_status', 'rejected');
            }
        }
        // Jika tidak ada filter status, tampilkan SEMUA laporan (pending, approved, rejected)

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('violation_date', $request->tanggal);
        }

        // Filter search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('student', function($s) use ($search) {
                    $s->where('name', 'like', "%$search%")
                      ->orWhere('nisn', 'like', "%$search%")
                      ->orWhereHas('currentAssignment.schoolClass', function($c) use ($search) {
                          $c->where('name', 'like', "%$search%")
                            ->orWhere('parallel_name', 'like', "%$search%") ;
                      });
                })
                ->orWhereHas('violationPoint', function($v) use ($search) {
                    $v->where('violation_type', 'like', "%$search%")
                      ->orWhere('violation_level', 'like', "%$search%") ;
                })
                ->orWhereHas('teacher', function($t) use ($search) {
                    $t->where('name', 'like', "%$search%") ;
                });
            });
        }

        $violations = $query->orderByDesc('created_at')->paginate(10)->appends($request->all());

        return view('violations.validation.index', compact('violations')); // Pastikan view ini ada
    }    public function show($id) // Sebaiknya gunakan Route Model Binding
    {
        // $violation = Violation::with(['student', 'violationPoint', 'teacher'])->findOrFail($id);
        // Dengan Route Model Binding:
        $violation = Violation::with(['student', 'violationPoint', 'teacher', 'validator'])->findOrFail($id);

        return view('violations.validation.show', compact('violation')); // Pastikan view ini ada
    }

    // Ganti parameter $id dengan Route Model Binding jika memungkinkan di route Anda
    public function validateViolation(Request $request, Violation $violation)
    {
        if (!Auth::user()->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat memvalidasi pelanggaran.');
        }
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string|max:1000' // Max length
        ]);

        $validatorTeacherId = null;
        if (Auth::check()) {
            $user = Auth::user();
            // Relasi ke Teacher (user->teacher) => NIP
            if ($user->teacher) {
                $validatorTeacherId = $user->teacher->nip;
            }
        }

        $updateData = [
            'validation_status' => $request->validation_status,
            'validation_notes' => $request->validation_notes,
            'validator_id' => $validatorTeacherId, // Simpan NIP Guru sebagai validator
            'validated_at' => now(),
        ];

        // Logika untuk memperbarui 'status' utama berdasarkan 'validation_status'
        if ($request->validation_status === 'approved' || $request->validation_status === 'rejected') {
            $updateData['status'] = 'completed'; // Ubah ke 'completed' baik disetujui maupun ditolak
        }

        $violation->update($updateData);

        return redirect()->route('violation-validations.index') // Pastikan nama route ini benar
            ->with('success', 'Pelanggaran berhasil divalidasi');
    }

    /**
     * Show the form for editing the validation decision (only for the validator Guru BK).
     * Redirect ke show page karena sekarang menggunakan modal popup.
     */
    public function editValidation(Violation $violation)
    {
        $user = Auth::user();
        if (!$user->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat mengedit validasi.');
        }
        // Hanya Guru BK yang memvalidasi laporan ini yang boleh edit
        if ($violation->validator_id !== $user->teacher->nip) {
            abort(403, 'Anda hanya dapat mengedit validasi yang Anda lakukan.');
        }

        // Redirect ke show page dengan fragment untuk membuka modal edit
        return redirect()->route('violation-validations.show', $violation->id)
            ->with('open_edit_modal', true);
    }

    /**
     * Update the validation decision (only for the validator Guru BK).
     */
    public function updateValidation(Request $request, Violation $violation)
    {
        $user = Auth::user();
        if (!$user->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat mengedit validasi.');
        }
        if ($violation->validator_id !== $user->teacher->nip) {
            abort(403, 'Anda hanya dapat mengedit validasi yang Anda lakukan.');
        }
        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string|max:1000',
        ], [
            'validation_notes.required_if' => 'Catatan validasi wajib diisi ketika menolak laporan.',
        ]);
        $violation->validation_status = $request->validation_status;
        $violation->validation_notes = $request->validation_notes;
        $violation->validated_at = now();
        $violation->save();
        return redirect()->route('violation-validations.show', $violation->id)
            ->with('success', 'Keputusan validasi berhasil diubah.');
    }
}
