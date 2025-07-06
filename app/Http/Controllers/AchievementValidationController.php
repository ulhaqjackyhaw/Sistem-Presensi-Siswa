<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementValidationController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:Guru BK');
    }

    public function index(Request $request)
    {
        $query = Achievement::with(['student', 'achievementPoint', 'teacher']);

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
            $query->whereDate('achievement_date', $request->tanggal);
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
                            ->orWhere('parallel_name', 'like', "%$search%");
                      });
                })
                ->orWhere('achievements_name', 'like', "%$search%")
                ->orWhereHas('achievementPoint', function($a) use ($search) {
                    $a->where('achievement_type', 'like', "%$search%")
                      ->orWhere('achievement_category', 'like', "%$search%");
                })
                ->orWhereHas('teacher', function($t) use ($search) {
                    $t->where('name', 'like', "%$search%");
                });
            });
        }

        $achievements = $query->orderByDesc('created_at')->paginate(10)->appends($request->all());

        return view('achievements.validation.index', compact('achievements'));
    }

    public function show($id)
    {
        $achievement = Achievement::with(['student', 'achievementPoint', 'teacher', 'validator'])->findOrFail($id);
        return view('achievements.validation.show', compact('achievement'));
    }

    public function validateAchievement(Request $request, Achievement $achievement)
    {
        if (!Auth::user()->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat memvalidasi prestasi.');
        }

        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string|max:1000'
        ], [
            'validation_notes.required_if' => 'Catatan validasi wajib diisi ketika menolak laporan.',
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

        $achievement->update($updateData);

        return redirect()->route('achievement-validations.index')
            ->with('success', 'Prestasi berhasil divalidasi');
    }

    /**
     * Show the form for editing the validation decision (only for the validator Guru BK).
     * Redirect ke show page karena sekarang menggunakan modal popup.
     */
    public function editValidation(Achievement $achievement)
    {
        $user = Auth::user();
        if (!$user->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat mengedit validasi.');
        }
        // Hanya Guru BK yang memvalidasi laporan ini yang boleh edit
        if ($achievement->validator_id !== $user->teacher->nip) {
            abort(403, 'Anda hanya dapat mengedit validasi yang Anda lakukan.');
        }

        // Redirect ke show page dengan fragment untuk membuka modal edit
        return redirect()->route('achievement-validations.show', $achievement->id)
            ->with('open_edit_modal', true);
    }

    /**
     * Update the validation decision (only for the validator Guru BK).
     */
    public function updateValidation(Request $request, Achievement $achievement)
    {
        $user = Auth::user();
        if (!$user->hasRole('Guru BK')) {
            abort(403, 'Hanya Guru BK yang dapat mengedit validasi.');
        }
        if ($achievement->validator_id !== $user->teacher->nip) {
            abort(403, 'Anda hanya dapat mengedit validasi yang Anda lakukan.');
        }

        $request->validate([
            'validation_status' => 'required|in:approved,rejected',
            'validation_notes' => 'required_if:validation_status,rejected|nullable|string|max:1000',
        ], [
            'validation_notes.required_if' => 'Catatan validasi wajib diisi ketika menolak laporan.',
        ]);

        $achievement->validation_status = $request->validation_status;
        $achievement->validation_notes = $request->validation_notes;
        $achievement->validated_at = now();
        $achievement->save();

        return redirect()->route('achievement-validations.show', $achievement->id)
            ->with('success', 'Keputusan validasi berhasil diubah.');
    }
}
