<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function index()
    {
        $curriculums = Curriculum::latest()->get();
        return view('curriculums.index', compact('curriculums'));
    }

    public function create()
    {
        return view('curriculums.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'curriculum_name' => 'required|string|max:255',
        ]);

        Curriculum::create([
            'curriculum_name' => $validated['curriculum_name'],
        ]);

        return redirect()->route('manage-curriculums.index')->with('success', 'Kurikulum berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        return view('curriculums.edit', compact('curriculum'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'curriculum_name' => 'required|string|max:255',
        ]);

        $curriculum = Curriculum::findOrFail($id);

        $curriculum->update([
            'curriculum_name' => $validated['curriculum_name'],
        ]);

        return redirect()->route('manage-curriculums.index')->with('success', 'Kurikulum berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $curriculum = Curriculum::findOrFail($id);
        
        $curriculum->delete();
        return redirect()->route('manage-curriculums.index')->with('success', 'Kurikulum berhasil dihapus.');
    }
}
