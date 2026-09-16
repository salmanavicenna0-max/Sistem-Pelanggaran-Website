<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Services\PointTransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointTransactionController extends Controller
{
    // --- API Endpoint untuk BK ---
    public function manualAchievement(Request $request, PointTransactionService $service): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'achievement_rule_id' => ['nullable', 'exists:achievement_rules,id'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $transaction = $service->recordManualAchievement(
            $student,
            $validated['points'],
            $validated['description'],
            $validated['achievement_rule_id'] ?? null,
            auth()->id()
        );

        return response()->json([
            'message' => 'Manual achievement recorded successfully.',
            'data' => $transaction,
        ]);
    }

    public function correction(Request $request, PointTransactionService $service): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'target_balance' => ['required', 'integer'],
            'reason' => ['required', 'string'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $transaction = $service->recordCorrection(
            $student,
            $validated['target_balance'],
            $validated['reason'],
            auth()->id()
        );

        return response()->json([
            'message' => 'Point balance corrected successfully.',
            'data' => $transaction,
        ]);
    }

    public function reversal(Request $request, PointTransaction $transaction, PointTransactionService $service): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        if (PointTransaction::where('reference_type', PointTransaction::class)->where('reference_id', $transaction->id)->exists()) {
            return response()->json(['message' => 'Transaction has already been reversed.'], 422);
        }

        $newTransaction = $service->recordReversal(
            $transaction,
            $validated['reason'],
            auth()->id()
        );

        return response()->json([
            'message' => 'Transaction reversed successfully.',
            'data' => $newTransaction,
        ]);
    }

    // --- API Endpoint untuk Siswa (Mobile & Web) ---
    public function studentHandbook(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            return response()->json(['message' => 'Student profile not found.'], 404);
        }

        $student->load(['schoolClass.schoolYear']);

        return response()->json([
            'student' => [
                'name' => $student->name,
                'nis' => $student->nis,
                'class' => $student->schoolClass->name,
            ],
            'points' => [
                'current_balance' => $student->getCurrentPoints(),
                'zone' => $student->getPointZone(),
            ],
        ]);
    }

    public function studentTransactions(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->student;

        if (! $student) {
            return response()->json(['message' => 'Student profile not found.'], 404);
        }

        $transactions = $student->pointTransactions()
            ->with(['performer', 'reference'])
            ->latest('transacted_at')
            ->latest('id')
            ->paginate(20);

        return response()->json($transactions);
    }
}
