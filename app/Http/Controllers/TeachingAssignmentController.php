<?php

namespace App\Http\Controllers;

use App\Models\TeachingAssignment;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeachingAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachingAssignments = TeachingAssignment::with([
            'academicYear',
            'class',
            'subject',
            'teacher'
        ])->get();

        return view('teaching_assignments.index', compact('teachingAssignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $academicYears = AcademicYear::where('is_active', 1)->get();
        $classes       = SchoolClass::all();
        $subjects      = Subject::where('is_active', 1)->get();
        $teachers      = Teacher::all();

        return view('teaching_assignments.create', compact(
            'academicYears', 'classes', 'subjects', 'teachers'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
/*         dd($request->all()); */
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'required|exists:classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,nip',
        ]);

        TeachingAssignment::create($validated);

        return redirect()->route('manage-teacher-subject-assignments.index')
                         ->with('success', 'Penugasan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeachingAssignment $teacherAssignment)
    {
        return view('teaching_assignments.show', compact('teacherAssignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeachingAssignment $teacherAssignment)
    {
        $academicYears = AcademicYear::where('is_active', 1)->get();
        $classes       = SchoolClass::all();
        $subjects      = Subject::where('is_active', 1)->get();
        $teachers      = Teacher::all();

        return view('teaching_assignments.edit', compact(
            'teacherAssignment', 'academicYears', 'classes', 'subjects', 'teachers'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeachingAssignment $teacherAssignment)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id'         => 'required|exists:classes,id',
            'subject_id'       => 'required|exists:subjects,id',
            'teacher_id'       => 'required|exists:teachers,nip',
        ]);

        $teacherAssignment->update($validated);

        return redirect()->route('manage-teacher-subject-assignments.index')
                         ->with('success', 'Penugasan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeachingAssignment $teacherAssignment)
    {
        $teacherAssignment->delete();

        return redirect()->route('manage-teacher-subject-assignments.index')
                         ->with('success', 'Penugasan berhasil dihapus.');
    }
}
