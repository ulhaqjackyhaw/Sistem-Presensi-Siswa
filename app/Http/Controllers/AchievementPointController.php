<?php

namespace App\Http\Controllers;

use App\Models\AchievementPoint;
use Illuminate\Http\Request;

class AchievementPointController extends Controller
{
    public function index()
    {
        $achievements = AchievementPoint::paginate(10);
        return view('achievement-management.index', compact('achievements'));
    }

    public function history()
    {
        $achievements = AchievementPoint::withTrashed()->get();
        return view('achievement-management.history', compact('achievements'));
    }

    public function create()
    {
        return view('achievement-management.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_prestasi' => 'required',
            'kategori_prestasi' => 'required',
            'poin' => 'required|numeric',
        ]);

        AchievementPoint::create($validated);
        return redirect()->route('achievement-management.index')->with('success', 'Achievement point created successfully.');
    }

    public function edit(AchievementPoint $achievement_management)
    {
        return view('achievement-management.edit', ['achievement' => $achievement_management]);
    }

    public function update(Request $request, AchievementPoint $achievement_management)
    {
        $validated = $request->validate([
            'jenis_prestasi' => 'required',
            'kategori_prestasi' => 'required',
            'poin' => 'required|numeric',
        ]);

        $achievement_management->update($validated);
        return redirect()->route('achievement-management.index')->with('success', 'Achievement point updated successfully.');
    }

    public function destroy(AchievementPoint $achievement_management)
    {
        $achievement_management->delete();
        return redirect()->route('achievement-management.index')->with('success', 'Achievement point deleted successfully.');
    }

    public function report()
    {
        $achievements = AchievementPoint::all();
        return view('achievement-management.report', compact('achievements'));
    }

    public function updateStatus($id, $status)
    {
        $achievement = AchievementPoint::findOrFail($id);
        $achievement->status = $status;
        $achievement->save();

        return redirect()->back()->with('success', 'Achievement status updated successfully.');
    }
}
