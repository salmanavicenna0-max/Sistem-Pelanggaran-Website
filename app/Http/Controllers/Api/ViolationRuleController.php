<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ViolationRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ViolationRuleController extends Controller
{
    public function index(): JsonResponse
    {
        $rules = ViolationRule::all();

        return response()->json($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:violation_rules,code',
            'name' => 'required|string|max:150',
            'points' => 'required|integer', // Usually negative, but we'll accept integer and enforce in model/logic if needed
            'severity' => 'required|in:ringan,sedang,berat,sangat_berat,khusus',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rule = ViolationRule::create($validated);

        return response()->json($rule, 201);
    }

    public function show(ViolationRule $violationRule): JsonResponse
    {
        return response()->json($violationRule);
    }

    public function update(Request $request, ViolationRule $violationRule): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('violation_rules')->ignore($violationRule->id)],
            'name' => 'required|string|max:150',
            'points' => 'required|integer',
            'severity' => 'required|in:ringan,sedang,berat,sangat_berat,khusus',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $violationRule->update($validated);

        return response()->json($violationRule);
    }

    public function destroy(ViolationRule $violationRule): JsonResponse
    {
        $violationRule->delete();

        return response()->json(null, 204);
    }
}
