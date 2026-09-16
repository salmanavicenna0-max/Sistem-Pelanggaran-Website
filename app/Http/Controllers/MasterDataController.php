<?php

namespace App\Http\Controllers;

use App\Models\AchievementRule;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\ViolationRule;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    // --- Aturan Disiplin & Prestasi ---
    public function rules(): View
    {
        $violationRules = ViolationRule::where('is_active', true)->get();
        $achievementRules = AchievementRule::where('is_active', true)->get();

        return view('master.rules', compact('violationRules', 'achievementRules'));
    }

    // --- Data Siswa & Import ---
    public function students(): View
    {
        $schoolYears = SchoolYear::where('is_active', true)->get();
        $schoolClasses = SchoolClass::with('homeroomTeacher')->get();

        return view('master.students', compact('schoolYears', 'schoolClasses'));
    }

    // --- Ekspor Rekap ---
    public function export(): View
    {
        return view('master.export');
    }

    public function exportPdf()
    {
        $students = \App\Models\Student::with('schoolClass', 'user')->get()->sortByDesc(fn($s) => $s->getCurrentPoints());
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', compact('students'))->setPaper('a4', 'landscape');
        return $pdf->stream('rekap-poin-siswa-'.date('YmdHis').'.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PointTransactionsExport, 'rekap-mutasi-poin-'.date('YmdHis').'.xlsx');
    }
}
