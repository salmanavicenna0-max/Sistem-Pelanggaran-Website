<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CaseAction;
use App\Models\StudentCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = StudentCase::query()
            ->with(['student.user', 'student.schoolClass', 'creator', 'report']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->query('student_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $cases = $query->latest('created_at')->get();

        return response()->json([
            'message' => 'Cases retrieved successfully.',
            'data' => $cases,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'report_id' => ['nullable', 'exists:reports,id', 'unique:cases,report_id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'confidentiality' => ['required', 'in:normal,confidential'],
        ]);

        $case = StudentCase::query()->create([
            'student_id' => $validated['student_id'],
            'report_id' => $validated['report_id'] ?? null,
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'open',
            'confidentiality' => $validated['confidentiality'],
        ]);

        $case->load(['student.user', 'student.schoolClass', 'creator', 'report']);

        return response()->json([
            'message' => 'Discipline case created successfully.',
            'data' => $case,
        ], 201);
    }

    public function show(StudentCase $case): JsonResponse
    {
        $case->load(['student.user', 'student.schoolClass', 'creator', 'report.violationRule', 'actions.performer']);

        return response()->json([
            'message' => 'Case details retrieved successfully.',
            'data' => $case,
        ]);
    }

    public function storeAction(Request $request, StudentCase $case): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
        ]);

        $action = CaseAction::query()->create([
            'case_id' => $case->id,
            'performed_by' => auth()->id(),
            'action' => $validated['action'],
            'note' => $validated['note'] ?? null,
            'performed_at' => now(),
        ]);

        if (! empty($validated['status'])) {
            $case->status = $validated['status'];
            if ($validated['status'] === 'closed') {
                $case->closed_at = now();
            } else {
                $case->closed_at = null;
            }
            $case->save();
        }

        $action->load('performer');

        return response()->json([
            'message' => 'Case action recorded successfully.',
            'data' => [
                'action' => $action,
                'case' => $case->fresh(['actions.performer']),
            ],
        ], 201);
    }
}
