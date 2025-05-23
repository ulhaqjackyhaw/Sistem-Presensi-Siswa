<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subject::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('kode', function ($row) {
                    return $row->code;
                })
                ->addColumn('nama', function ($row) {
                    return $row->name;
                })
                ->addColumn('deskripsi', function ($row) {
                    return $row->description ?? '-';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = route('subjects.edit', $row->id);
                    $deleteFunc = "deleteSubject($row->id)";
                    return '
                        <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                        <button onclick="' . $deleteFunc . '" class="btn btn-sm btn-danger">Hapus</button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('subjects.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:10|unique:subjects,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $validatedData['is_active'] = 1;

        Subject::create($validatedData);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:10|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $subject->update($validatedData);

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}