<?php

namespace App\Http\Controllers;

use App\Models\Hour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HourController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:read_hours')->only('index');
        $this->middleware('permission:create_hours')->only('create', 'store');
        $this->middleware('permission:update_hours')->only('edit', 'update');
        $this->middleware('permission:delete_hours')->only('destroy');
    }

    public function index()
    {
        if (request()->ajax()) {
            $hours = Hour::select('hours.*');

            return DataTables::of($hours)
                ->addIndexColumn()
                ->addColumn('action', function ($hour) {
                    $actions = '';
                    if (Auth::check()) {
                        $actions .= "<a href='" . route('manage-hours.edit', $hour->id) . "' class='btn btn-sm btn-info mr-1'><i class='fas fa-edit'></i></a>";
                        $actions .= "<button class='btn btn-sm btn-danger' onclick='deleteHour(\"{$hour->id}\")'><i class='fas fa-trash'></i></button>";
                    }
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('manage-hours.index');
    }

    public function create()
    {
        return view('manage-hours.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan pilih nomor lain.',
        ]);

        Hour::create([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil ditambahkan.');
    }

    public function edit($hour)
    {
        $hour = Hour::findOrFail($hour);
        if (!$hour) {
            return redirect()->route('manage-hours.index')->with('error', 'Jam tidak ditemukan.');
        }
        return view('manage-hours.edit', compact('hour'));
    }

    public function update(Request $request, $hour)
    {
        $hour = Hour::findOrFail($hour);
        if (!$hour) {
            return redirect()->route('manage-hours.index')->with('error', 'Jam tidak ditemukan.');
        }
        $request->validate([
            'session_type' => 'required|in:Jam pelajaran,Jam istirahat',
            'slot_number' => 'required|integer|min:1|unique:hours,slot_number,' . $hour->id,
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ], [
            'slot_number.unique' => 'Nomor jam tersebut sudah digunakan. Silakan masukkan nomor lain.',
        ]);

        $hour->update([
            'session_type' => $request->session_type,
            'slot_number' => $request->slot_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil diperbarui.');
    }

    public function destroy($hour)
    {
        $hour = Hour::findOrFail($hour);
        $hour->delete();
        if (request()->ajax()) {
            return response()->json(['message' => 'Jam berhasil dihapus.']);
        }
        return redirect()->route('manage-hours.index')->with('success', 'Jam berhasil dihapus.');
    }
}