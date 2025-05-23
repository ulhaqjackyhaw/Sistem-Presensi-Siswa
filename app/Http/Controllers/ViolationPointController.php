<?php

namespace App\Http\Controllers;

use App\Models\ViolationPoint;
use Illuminate\Http\Request;

class ViolationPointController extends Controller
{
    public function index(Request $request)
    {
        $query = ViolationPoint::query();

        // Pencarian berdasarkan input "search"
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('violation_type', 'like', "%{$search}%")
                  ->orWhere('points', 'like', "%{$search}%");
            });
        }

        // Pagination berdasarkan jumlah "entries" yang dipilih
        $entries = $request->input('entries', 10); // Default 10
        $violationPoints = $query->paginate($entries)->appends($request->query());

        return view('manage-violations.index', compact('violationPoints'));
    }

    public function create()
    {
        return view('manage-violations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'violation_type' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        ViolationPoint::create($request->all());
        return redirect()->route('violation-management.index')->with('success', 'Data pelanggaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $violationPoint = ViolationPoint::findOrFail($id);
        return view('manage-violations.edit', compact('violationPoint'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'violation_type' => 'required|string',
            'violation_level' => 'required|string',
            'points' => 'required|integer|min:1',
        ]);

        $violationPoint = ViolationPoint::findOrFail($id);
        $violationPoint->update($request->all());

        return redirect()->route('violation-management.index')->with('success', 'Data pelanggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $violationPoint = ViolationPoint::findOrFail($id);
        $violationPoint->delete();
        return redirect()->route('violation-management.index')->with('success', 'Data pelanggaran berhasil dihapus.');
    }
}
