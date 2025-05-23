<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Student;
use App\Models\AchievementPoint;
use App\Models\AcademicYear;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::with(['student', 'achievementPoint', 'academicYear', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('achievements.index', compact('achievements'));
    }

    public function create()
    {
        $students = Student::all();
        $achievementPoints = AchievementPoint::all();
        $academicYears = AcademicYear::all();
        $teachers = Teacher::all();

        return view('achievements.create', compact('students', 'achievementPoints', 'academicYears', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'achievements_name' => 'required|string|max:255',
            'achievement_points_id' => 'required|exists:achievement_points,id',
            'achievement_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'awarded_by' => 'nullable|exists:teachers,id',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $achievement = new Achievement($request->except('evidence'));

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
        $achievement->load(['student', 'achievementPoint', 'academicYear', 'teacher']);
        return view('achievements.show', compact('achievement'));
    }

    public function edit(Achievement $achievement)
    {
        $students = Student::all();
        $achievementPoints = AchievementPoint::all();
        $academicYears = AcademicYear::all();
        $teachers = Teacher::all();

        return view('achievements.edit', compact('achievement', 'students', 'achievementPoints', 'academicYears', 'teachers'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'achievements_name' => 'required|string|max:255',
            'achievement_points_id' => 'required|exists:achievement_points,id',
            'achievement_date' => 'required|date',
            'academic_year_id' => 'required|exists:academic_years,id',
            'description' => 'required|string',
            'awarded_by' => 'nullable|exists:teachers,id',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status' => 'required|in:pending,processed,completed,rejected'
        ]);

        DB::beginTransaction();
        try {
            $achievement->fill($request->except('evidence'));

            if ($request->hasFile('evidence')) {
                // Hapus file lama jika ada
                if ($achievement->evidence) {
                    Storage::disk('public')->delete($achievement->evidence);
                }

                $path = $request->file('evidence')->store('achievements/evidence', 'public');
                $achievement->evidence = $path;
            }

            $achievement->save();

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
}
