<?php

namespace App\Http\Controllers;

use App\Models\AchievementRule;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Services\PointTransactionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointController extends Controller
{
    // --- Buku Saku (Siswa) atau Rekap Poin (Guru/BK/Wali Kelas) ---
    public function index(): View
    {
        if (auth()->user()->role === 'siswa') {
            $student = auth()->user()->student;
            if (! $student) {
                return redirect()->route('dashboard')->with('error', 'Profil siswa tidak ditemukan.');
            }

            $currentPoints = $student->getCurrentPoints();
            $zone = $student->point_zone;

            // Recent transactions
            $recentTransactions = PointTransaction::query()
                ->where('student_id', $student->id)
                ->latest('transacted_at')
                ->take(10)
                ->get();

            return view('points.siswa', compact('student', 'currentPoints', 'zone', 'recentTransactions'));
        }

        // Untuk BK, Wali Kelas, Guru: Tampilkan Rekap Seluruh Siswa
        $students = Student::query()
            ->with(['user', 'schoolClass'])
            ->where('is_active', true)
            ->get();

        return view('points.index', compact('students'));
    }

    // --- BK: Riwayat Poin Siswa ---
    public function riwayat(Request $request): View
    {
        $studentId = $request->student_id;

        $student = Student::with(['user', 'schoolClass'])->findOrFail($studentId);
        $transactions = PointTransaction::query()
            ->with('performer')
            ->where('student_id', $studentId)
            ->latest('transacted_at')
            ->latest('id')
            ->get();

        $achievementRules = AchievementRule::where('is_active', true)->orderBy('name')->get();

        return view('points.bk-riwayat', compact('student', 'transactions', 'achievementRules'));
    }

    public function storeManualAchievement(Request $request, PointTransactionService $service)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'achievement_rule_id' => ['nullable', 'exists:achievement_rules,id'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $service->recordManualAchievement(
            $student,
            $validated['points'],
            $validated['description'],
            $validated['achievement_rule_id'] ?? null,
            auth()->id()
        );

        return back()->with('status', 'Penghargaan manual berhasil ditambahkan.');
    }

    public function storeCorrection(Request $request, PointTransactionService $service)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'target_balance' => ['required', 'integer'],
            'reason' => ['required', 'string'],
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $service->recordCorrection(
            $student,
            $validated['target_balance'],
            $validated['reason'],
            auth()->id()
        );

        return back()->with('status', 'Saldo poin berhasil dikoreksi.');
    }

    public function storeReversal(Request $request, PointTransaction $transaction, PointTransactionService $service)
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        if (PointTransaction::where('reference_type', PointTransaction::class)->where('reference_id', $transaction->id)->exists()) {
            return back()->with('error', 'Transaksi ini sudah pernah dibatalkan.');
        }

        $service->recordReversal(
            $transaction,
            $validated['reason'],
            auth()->id()
        );

        return back()->with('status', 'Transaksi berhasil dibatalkan (Reversal).');
    }
}
