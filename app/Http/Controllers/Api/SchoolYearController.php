<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolYearController extends Controller
{
    public function index(): JsonResponse
    {
        $schoolYears = SchoolYear::orderByDesc('starts_on')->get();

        return response()->json($schoolYears);
    }

    public function store(Request $request): JsonResponse
    {
        // Normalize input if start_date/end_date is provided
        if ($request->has('start_date') && ! $request->has('starts_on')) {
            $request->merge(['starts_on' => $request->start_date]);
        }
        if ($request->has('end_date') && ! $request->has('ends_on')) {
            $request->merge(['ends_on' => $request->end_date]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after:starts_on',
            'is_active' => 'boolean',
        ]);

        $schoolYear = SchoolYear::create($validated);

        return response()->json($schoolYear, 201);
    }

    public function show(SchoolYear $schoolYear): JsonResponse
    {
        return response()->json($schoolYear);
    }

    public function update(Request $request, SchoolYear $schoolYear): JsonResponse
    {
        if ($request->has('start_date') && ! $request->has('starts_on')) {
            $request->merge(['starts_on' => $request->start_date]);
        }
        if ($request->has('end_date') && ! $request->has('ends_on')) {
            $request->merge(['ends_on' => $request->end_date]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'starts_on' => 'required|date',
            'ends_on' => 'required|date|after:starts_on',
            'is_active' => 'boolean',
        ]);

        $schoolYear->update($validated);

        return response()->json($schoolYear);
    }

    public function destroy(SchoolYear $schoolYear): JsonResponse
    {
        $schoolYear->delete();

        return response()->json(null, 204);
    }
}
