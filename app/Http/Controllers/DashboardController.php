<?php

namespace App\Http\Controllers;

use App\Models\AchievementRule;
use App\Models\PointTransaction;
use App\Models\Report;
use App\Models\Student;
use App\Models\StudentCase;
use App\Models\ViolationRule;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        // --- SISWA: Only own data ---
        if ($user->role === 'siswa') {
            $student = $user->student;
            if (! $student) {
                return redirect()->route('login')->withErrors(['login' => 'Profil siswa tidak ditemukan.']);
            }

            $currentPoints = $student->getCurrentPoints();
            $zone = $student->point_zone;

            // Recent transactions
            $recentTransactions = PointTransaction::query()
                ->where('student_id', $student->id)
                ->latest('transacted_at')
                ->take(10)
                ->get();

            return view('dashboard.siswa', compact('student', 'currentPoints', 'zone', 'recentTransactions'));
        }

        // --- GURU: Standard ---
        if ($user->role === 'guru') {
            $student = $user->student;
            $reports = Report::query()
                ->when($student, fn ($q) => $q->where('student_id', $student->id))
                ->latest('created_at')
                ->take(10)
                ->get();

            $totalReports = Report::query()
                ->when($student, fn ($q) => $q->where('student_id', $student->id))
                ->count();

            $approvedReports = Report::query()
                ->when($student, fn ($q) => $q->where('student_id', $student->id))
                ->where('status', 'approved')
                ->count();

            $pendingReports = Report::query()
                ->when($student, fn ($q) => $q->where('student_id', $student->id))
                ->where('status', 'pending')
                ->count();

            return view('dashboard.guru', compact('student', 'reports', 'totalReports', 'approvedReports', 'pendingReports'));
        }

        // --- GURI + WALI KELAS ---
        if ($user->isHomeroomTeacher()) {
            $student = $user->student;
            $class = $student?->schoolClass;

            // Students in this class
            $classStudents = Student::query()
                ->where('class_id', $class?->id)
                ->with(['user', 'pointTransactions' => fn ($q) => $q->latest('transacted_at')->take(1)])->get();

            // Class reports
            $classReports = Report::query()
                ->when($classStudents->count(), fn ($q) => $q->whereIn('student_id', $classStudents->pluck('id')))
                ->latest('created_at')
                ->take(10)
                ->get();

            // Class point summary
            $classPointSummary = $classStudents->map(fn ($s) => [
                'name' => $s->name,
                'nis' => $s->nis,
                'currentPoints' => $s->getCurrentPoints(),
                'zone' => $s->point_zone,
            ])->sortByDesc(fn ($s) => $s['currentPoints']);

            return view('dashboard.wali-kelas', compact('student', 'class', 'classStudents', 'classReports', 'classPointSummary'));
        }

        // --- KESISWAAN / BK ---
        $classId = request('class_id');
        $startDate = request('start_date');
        $endDate = request('end_date');

        $studentQuery = Student::query()
            ->when($classId, fn ($q) => $q->where('class_id', $classId));

        $totalStudents = $studentQuery->count();

        $reportQuery = Report::query()
            ->when($classId, function($q) use ($classId) {
                $q->whereHas('student', fn($sq) => $sq->where('class_id', $classId));
            })
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate));

        // Stats cards data
        $stats = [
            'total_students' => $totalStudents,
            'total_reports' => (clone $reportQuery)->count(),
            'pending_approval' => (clone $reportQuery)->where('status', 'pending')->count(),
            'active_cases' => StudentCase::where('status', 'in_progress')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->when($classId, function($q) use ($classId) {
                    $q->whereHas('report', function($rq) use ($classId) {
                        $rq->whereHas('student', fn($sq) => $sq->where('class_id', $classId));
                    });
                })->count(),
        ];

        // Zone distribution chart data
        $zoneDistribution = (clone $studentQuery)->withCount('pointTransactions')
            ->get()
            ->map(fn ($s) => $s->point_zone['name'])
            ->countBy();

        $zoneOrder = ['Istimewa', 'Luar Biasa', 'Normal', 'Pembinaan 1', 'Pembinaan 2', 'Pembinaan 3', 'Pembinaan 4', 'Intervensi Khusus'];
        $zoneData = array_combine($zoneOrder, array_map(fn ($v) => $zoneDistribution[$v] ?? 0, $zoneOrder));

        $statusDistribution = (clone $reportQuery)->pluck('status')->countBy();
        $statusData = [
            'Pending' => $statusDistribution['pending'] ?? 0,
            'Approved' => $statusDistribution['approved'] ?? 0,
            'Rejected' => $statusDistribution['rejected'] ?? 0,
        ];

        // Top students by points
        $topStudents = (clone $studentQuery)->with('user')
            ->withCount('pointTransactions')
            ->get()
            ->sortByDesc(fn ($s) => $s->getCurrentPoints())
            ->take(5);

        // Recent transactions for BK overview
        $recentTransactions = PointTransaction::query()
            ->when($classId, function($q) use ($classId) {
                $q->whereHas('student', fn($sq) => $sq->where('class_id', $classId));
            })
            ->when($startDate, fn($q) => $q->whereDate('transacted_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('transacted_at', '<=', $endDate))
            ->latest('transacted_at')
            ->take(10)
            ->get();

        $classes = \App\Models\SchoolClass::orderBy('grade_level')->orderBy('name')->get();

        return view('dashboard.bk', compact('stats', 'zoneData', 'statusData', 'topStudents', 'recentTransactions', 'classes'));
    }
}
