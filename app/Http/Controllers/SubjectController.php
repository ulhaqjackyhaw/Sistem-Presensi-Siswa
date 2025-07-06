<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Curriculum;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::latest()->get();
        return view('subjects.index', compact('subjects'));
    }

    public function create()
    {
        $curriculums = Curriculum::all();
        return view('subjects.create', compact('curriculums'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_code' => 'required|string|max:10|unique:subjects,subject_code',
            'subject_name' => 'required|string|max:255',
            'curriculum_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Subject::create([
            'subject_code' => $validated['subject_code'],
            'subject_name' => $validated['subject_name'],
            'curriculum_name' => $validated['curriculum_name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('manage-subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        $curriculums = Curriculum::all();
        return view('subjects.edit', compact('subject', 'curriculums'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_code' => 'required|string|max:10|unique:subjects,subject_code,' . $subject->id,
            'subject_name' => 'required|string|max:255',
            'curriculum_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $subject->update([
            'subject_code' => $validated['subject_code'],
            'subject_name' => $validated['subject_name'],
            'curriculum_name' => $validated['curriculum_name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('manage-subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('manage-subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
