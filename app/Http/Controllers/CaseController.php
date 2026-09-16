<?php

namespace App\Http\Controllers;

use App\Models\CaseAction;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseController extends Controller
{
    public function index(): View
    {
        $cases = StudentCase::query()
            ->with(['student.user', 'student.schoolClass', 'creator', 'report'])
            ->latest('created_at')
            ->get();

        return view('cases.index', compact('cases'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        $report = null;
        if ($request->filled('report_id')) {
            $report = Report::with(['student.user', 'student.schoolClass', 'violationRule'])->find($request->query('report_id'));

            if ($report) {
                $existingCase = StudentCase::where('report_id', $report->id)->first();
                if ($existingCase) {
                    return redirect()->route('cases.show', $existingCase)
                        ->with('status', 'Laporan ini sudah memiliki kasus pembinaan terkait.');
                }
            }
        }

        $students = Student::with(['user', 'schoolClass'])->where('is_active', true)->get();

        return view('cases.create', compact('students', 'report'));
    }

    public function store(Request $request): RedirectResponse
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

        return redirect()->route('cases.show', $case)->with('status', 'Kasus pembinaan baru berhasil dibuka.');
    }

    public function show(StudentCase $case): View
    {
        $case->load(['student.user', 'student.schoolClass', 'creator', 'report.violationRule', 'actions.performer']);

        return view('cases.show', compact('case'));
    }

    public function storeAction(Request $request, StudentCase $case): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
        ]);

        CaseAction::query()->create([
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

        return back()->with('status', 'Tindakan pembinaan berhasil dicatat.');
    }
}
