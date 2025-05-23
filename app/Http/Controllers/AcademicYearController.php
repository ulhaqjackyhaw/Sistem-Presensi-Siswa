<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::all();
        return view('academic_years.index', compact('academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('academic_years.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100',
            'end_year' => 'required|integer|gt:start_year',
            'semester' => 'required|in:0,1',
            'is_active' => 'required|in:0,1',
        ]);

        AcademicYear::create([
            'start_year' => $request->start_year,
            'end_year' => $request->end_year,
            'semester' => $request->semester,
            'is_active' => $request->is_active,
        ]);

        return redirect()->route('manage-academic-years.index')->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $manage_academic_year)
    {
        return view('academic_years.edit', ['academicYear' => $manage_academic_year]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AcademicYear $manage_academic_year)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'semester' => 'required|in:ganjil,genap',
        ]);

        $manage_academic_year->update($request->only('tahun', 'semester'));

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $manage_academic_year)
    {
        $manage_academic_year->delete();

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }
}
