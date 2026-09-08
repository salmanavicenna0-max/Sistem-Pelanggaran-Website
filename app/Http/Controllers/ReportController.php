<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\Report;
use App\Models\Student;
use App\Models\ViolationRule;
use App\Models\AchievementRule;
use App\Models\ReportAttachment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'password.changed']);
    }

    // --- SISWA: Buat laporan baru ---
    public function create(): View
    {
        return view('reports.siswa-create');
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'student_nis' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:violation,achievement'],
            'violation_rule_id' => ['required', 'exists:violation_rules,id', fn($val) => $request->type === 'violation'],
            'achievement_rule_id' => ['required', 'exists:achievement_rules,id', fn($val) => $request->type === 'achievement'],
            'occurred_on' => ['required', 'date'],
            'description' => ['required', 'string'],
            'photo' => ['sometimes', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
        ]);

        $student = Student::where('nis', $validated['student_nis'])->firstOrFail();
        $user = $student->user;

        $reportData = [
            'student_id' => $student->id,
            'reported_by' => $user->id,
            'verified_by' => null,
            'type' => $validated['type'],
            'occurred_on' => $validated['occurred_on'],
            'description' => $validated['description'],
            'status' => 'pending',
        ];

        if ($validated['type'] === 'violation') {
            $reportData['violation_rule_id'] = $validated['violation_rule_id'];
        } else {
            $reportData['achievement_rule_id'] = $validated['achievement_rule_id'];
        }

        $report = Report::query()->create($reportData);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('report_photos', 'private');
            ReportAttachment::query()->create([
                'report_id' => $report->id,
                'uploaded_by' => $user->id,
                'disk' => 'private',
                'path' => $path,
                'original_name' => $request->file('photo')->getClientOriginalName(),
                'mime_type' => $request->file('photo')->getClientOriginalMimeType(),
                'size' => $request->file('photo')->getSize(),
            ]);
        }

        return back()->with('status', 'Laporan berhasil dikirim. Status: pending menunggu verifikasi BK.');
    }

    // --- BK / Kesiswaan: Daftar laporan pending ---
    public function index(): View
    {
        $reports = Report::query()
            ->where('status', 'pending')
            ->latest('created_at')
            ->with(['student.user', 'violationRule', 'achievementRule'])
            ->get();

        return view('reports.bk-index', compact('reports'));
    }

    // --- Verifikasi / Tolak Laporan ---
    public function verify(Request $request, Report $report): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'verification_note' => ['sometimes', 'string'],
        ]);

        $report->update([
            'status' => $validated['status'],
            'verification_note' => $validated['verification_note'] ?? null,
            'verified_by' => auth()->user()->id,
            'verified_at' => now(),
        ]);

        // Jika approved, tambah/kurangi poin
        if ($validated['status'] === 'approved') {
            $student = $report->student;
            $currentPoints = $student->getCurrentPoints();
            $newPoints = match ($report->type) {
                'achievement' => $currentPoints + ($report->achievementRule?->points ?? 0),
                'violation' => max(0, $currentPoints + ($report->violationRule?->points ?? 0)),
            };

            // Buat transaksi poin
            $student->pointTransactions()->create([
                'student_id' => $student->id,
                'school_year_id' => $student->schoolClass->schoolYear->id,
                'performed_by' => auth()->user()->id,
                'reference_type' => Report::class,
                'reference_id' => $report->id,
                'type' => $report->type === 'achievement' ? 'achievement' : 'violation',
                'points' => $report->type === 'achievement' ? ($report->achievementRule?->points ?? 0) : (-$report->violationRule?->points ?? 0),
                'balance_before' => $currentPoints,
                'balance_after' => $newPoints,
                'description' => $report->type === 'achievement'
                    ? 'Penambahan poin: ' . $report->description
                    : 'Pengurangan poin: ' . $report->description,
                'transacted_at' => now(),
            ]);

            // Update saldo siswa
            $student->update(['is_active' => true]); // ensure active
        }

        return back()->with('status', 'Laporan ' . $validated['status'] . '. ' . ($validated['status'] === 'approved' ? 'Poin telah diperbarui.' : 'Catatan verifikasi tercatat.'));
    }

    // --- Lihat detail laporan ---
    public function show(Report $report): View
    {
        $report->load(['student.user', 'violationRule', 'achievementRule', 'attachments']);
        return view('reports.show', compact('report'));
    }
}