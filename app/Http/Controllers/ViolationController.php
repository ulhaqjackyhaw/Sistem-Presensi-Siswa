<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Violation;

class ViolationController extends Controller
{
    public function index()
    {
        return view('violations.index');
    }

    public function create()
    {
        return view('violations.create');
    }

    public function edit(Violation $violation)
    {
        return view('violations.edit', compact('violation'));
    }
}
