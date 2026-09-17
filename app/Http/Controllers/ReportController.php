<?php

namespace App\Http\Controllers;

use App\Models\AchievementRule;
use App\Models\Report;
use App\Models\ReportAttachment;
use App\Models\Student;
use App\Models\ViolationRule;
use App\Services\PointTransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    // --- Buat laporan baru ---
    public function create(): View
    {
        $violationRules = ViolationRule::where('is_active', true)->orderBy('name')->get();
        $achievementRules = AchievementRule::where('is_active', true)->orderBy('name')->get();
        $students = Student::with(['user', 'schoolClass'])->where('is_active', true)->get();

        return view('reports.siswa-create', compact('violationRules', 'achievementRules', 'students'));
    }

    public function store(Request $request, PointTransactionService $pointService): RedirectResponse
    {
        $validated = $request->validate([
            'student_nis' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:violation,achievement'],
            'violation_rule_id' => ['nullable', 'required_if:type,violation', 'exists:violation_rules,id'],
            'achievement_rule_id' => ['nullable', 'required_if:type,achievement', 'exists:achievement_rules,id'],
            'occurred_on' => ['required', 'date'],
            'description' => ['required', 'string'],
            'photo' => ['sometimes', 'nullable', 'image', 'max:2048', 'mimes:jpeg,png,jpg'],
        ]);

        $student = Student::where('nis', $validated['student_nis'])->firstOrFail();

        $isBK = auth()->user()->hasRole('kesiswaan_bk');

        $reportData = [
            'student_id' => $student->id,
            'reported_by' => auth()->id(),
            'verified_by' => $isBK ? auth()->id() : null,
            'verified_at' => $isBK ? now() : null,
            'type' => $validated['type'],
            'occurred_on' => $validated['occurred_on'],
            'description' => $validated['description'],
            'status' => $isBK ? 'approved' : 'pending',
            'violation_rule_id' => $validated['type'] === 'violation' ? $validated['violation_rule_id'] : null,
            'achievement_rule_id' => $validated['type'] === 'achievement' ? $validated['achievement_rule_id'] : null,
        ];

        $report = Report::query()->create($reportData);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('report_photos', 'public');
            ReportAttachment::query()->create([
                'report_id' => $report->id,
                'uploaded_by' => auth()->id(),
                'disk' => 'public',
                'path' => $path,
                'original_name' => $request->file('photo')->getClientOriginalName(),
                'mime_type' => $request->file('photo')->getClientMimeType(),
                'size' => $request->file('photo')->getSize(),
            ]);
        }

        if ($report->status === 'approved') {
            $pointService->recordReportApproval($report, auth()->id());
        }

        $msg = $report->status === 'approved' 
            ? 'Laporan berhasil dicatat dan poin langsung diperbarui.'
            : 'Laporan berhasil dikirim. Status: pending menunggu verifikasi BK.';

        return redirect()->route('dashboard')->with('status', $msg);
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
    public function verify(Request $request, Report $report, PointTransactionService $pointService): RedirectResponse
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

        // Jika approved, tambah/kurangi poin menggunakan service
        if ($validated['status'] === 'approved') {
            $pointService->recordReportApproval($report, auth()->user()->id);
        }

        return back()->with('status', 'Laporan '.$validated['status'].'. '.($validated['status'] === 'approved' ? 'Poin telah diperbarui.' : 'Catatan verifikasi tercatat.'));
    }

    // --- Lihat detail laporan ---
    public function show(Report $report): View
    {
        $report->load(['student.user', 'violationRule', 'achievementRule', 'attachments', 'studentCase']);

        return view('reports.show', compact('report'));
    }
}
