<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Pastikan Rule sudah di-import

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $academicYears = AcademicYear::orderBy('start_year', 'desc')->orderBy('semester', 'desc')->get();
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
            'start_year' => 'required|integer|min:2000|max:2100|digits:4',
            'end_year' => 'required|integer|gt:start_year|digits:4',
            'semester' => [
                'required',
                'in:0,1',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('start_year', $request->start_year)
                                 ->where('semester', $request->semester);
                }),
            ],
            'is_active' => 'required|boolean',
        ], [
            'semester.unique' => 'Kombinasi Tahun Mulai dan Semester ini sudah ada.',
        ]);

        if ($request->input('is_active') == '1') {
            AcademicYear::where('is_active', 1)->update(['is_active' => 0]);
        }

        AcademicYear::create($request->all());

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
        // ## AWAL BLOK LOGIKA BARU ##
        // Periksa apakah ini adalah permintaan update status cepat dari halaman index
        if ($request->has('status')) {
            $request->validate(['status' => 'required|boolean']);
            $newStatus = $request->input('status');

            // Jika status baru adalah 'Aktif', nonaktifkan yang lain
            if ($newStatus == 1) {
                AcademicYear::where('is_active', 1)
                            ->where('id', '!=', $manage_academic_year->id)
                            ->update(['is_active' => 0]);
            }
            // Update status untuk tahun akademik yang dipilih
            $manage_academic_year->is_active = $newStatus;
            $manage_academic_year->save();

            // Kirim respons JSON untuk AJAX
            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.'
            ]);
        }
        // ## AKHIR BLOK LOGIKA BARU ##

        // Jika bukan, jalankan logika update penuh dari form edit (kode yang sudah ada)
        $validatedData = $request->validate([
            'start_year' => 'required|integer|min:2000|max:2100|digits:4',
            'end_year' => 'required|integer|gt:start_year|digits:4',
            'semester' => [
                'required',
                'in:0,1',
                Rule::unique('academic_years')->where(function ($query) use ($request) {
                    return $query->where('start_year', $request->start_year)
                                 ->where('semester', $request->semester);
                })->ignore($manage_academic_year->id),
            ],
            'is_active' => 'required|boolean',
        ], [
            'semester.unique' => 'Kombinasi Tahun Mulai dan Semester ini sudah ada.',
        ]);

        if ($request->input('is_active') == '1') {
            AcademicYear::where('id', '!=', $manage_academic_year->id)
                        ->where('is_active', 1)
                        ->update(['is_active' => 0]);
        }

        $manage_academic_year->update($validatedData);

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AcademicYear $manage_academic_year)
    {
        if ($manage_academic_year->is_active) {
            return redirect()->route('manage-academic-years.index')
                ->with('error', 'Tidak dapat menghapus tahun akademik yang sedang aktif.');
        }

        $manage_academic_year->delete();

        return redirect()->route('manage-academic-years.index')
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }
}