<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(): JsonResponse
    {
        $schoolClasses = SchoolClass::with(['schoolYear', 'homeroomTeacher'])->get();

        return response()->json($schoolClasses);
    }

    public function store(Request $request): JsonResponse
    {
        if ($request->has('grade') && ! $request->has('grade_level')) {
            $gradeMap = ['X' => 10, 'XI' => 11, 'XII' => 12, '10' => 10, '11' => 11, '12' => 12];
            $request->merge(['grade_level' => $gradeMap[$request->grade] ?? (int) $request->grade]);
        }

        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:100',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        $schoolClass = SchoolClass::create($validated);
        $schoolClass->load(['schoolYear', 'homeroomTeacher']);

        return response()->json($schoolClass, 201);
    }

    public function show(SchoolClass $schoolClass): JsonResponse
    {
        $schoolClass->load(['schoolYear', 'homeroomTeacher']);

        return response()->json($schoolClass);
    }

    public function update(Request $request, SchoolClass $schoolClass): JsonResponse
    {
        if ($request->has('grade') && ! $request->has('grade_level')) {
            $gradeMap = ['X' => 10, 'XI' => 11, 'XII' => 12, '10' => 10, '11' => 11, '12' => 12];
            $request->merge(['grade_level' => $gradeMap[$request->grade] ?? (int) $request->grade]);
        }

        $validated = $request->validate([
            'school_year_id' => 'required|exists:school_years,id',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:100',
            'grade_level' => 'required|integer|min:1|max:12',
        ]);

        $schoolClass->update($validated);
        $schoolClass->load(['schoolYear', 'homeroomTeacher']);

        return response()->json($schoolClass);
    }

    public function destroy(SchoolClass $schoolClass): JsonResponse
    {
        $schoolClass->delete();

        return response()->json(null, 204);
    }
}
