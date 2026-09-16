<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AchievementRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AchievementRuleController extends Controller
{
    public function index(): JsonResponse
    {
        $rules = AchievementRule::all();

        return response()->json($rules);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:achievement_rules,code',
            'name' => 'required|string|max:150',
            'points' => 'required|integer|min:1',
            'pillar' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $rule = AchievementRule::create($validated);

        return response()->json($rule, 201);
    }

    public function show(AchievementRule $achievementRule): JsonResponse
    {
        return response()->json($achievementRule);
    }

    public function update(Request $request, AchievementRule $achievementRule): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', Rule::unique('achievement_rules')->ignore($achievementRule->id)],
            'name' => 'required|string|max:150',
            'points' => 'required|integer|min:1',
            'pillar' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $achievementRule->update($validated);

        return response()->json($achievementRule);
    }

    public function destroy(AchievementRule $achievementRule): JsonResponse
    {
        $achievementRule->delete();

        return response()->json(null, 204);
    }
}
