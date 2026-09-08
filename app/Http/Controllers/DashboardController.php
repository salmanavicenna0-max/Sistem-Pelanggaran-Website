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
            $zone = $student->getPointZone;

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
                'zone' => $s->getPointZone['name'],
            ])->sortByDesc(fn ($s) => $s['currentPoints']);

            return view('dashboard.wali-kelas', compact('student', 'class', 'classStudents', 'classReports', 'classPointSummary'));
        }

        // --- KESISWAAN / BK ---
        $totalStudents = Student::count();
        $totalViolations = ViolationRule::where('is_active', true)->count();
        $totalAchievements = AchievementRule::where('is_active', true)->count();

        // Stats cards data
        $stats = [
            'total_students' => $totalStudents,
            'total_reports' => Report::count(),
            'pending_approval' => Report::where('status', 'pending')->count(),
            'active_cases' => StudentCase::where('status', 'in_progress')->count(),
        ];

        // Zone distribution chart data
        $zoneDistribution = Student::withCount('pointTransactions')
            ->get()
            ->map(fn ($s) => $s->getPointZone['name'])
            ->countBy();

        $zoneOrder = ['Istimewa', 'Luar Biasa', 'Normal', 'Pembinaan 1', 'Pembinaan 2', 'Pembinaan 3', 'Pembinaan 4', 'Intervensi Khusus'];
        $zoneData = array_combine($zoneOrder, array_map(fn ($v) => $zoneDistribution[$v] ?? 0, $zoneOrder));

        // Top students by points
        $topStudents = Student::with('user')
            ->withCount('pointTransactions')
            ->get()
            ->sortByDesc(fn ($s) => $s->getCurrentPoints())
            ->take(5);

        // Recent transactions for BK overview
        $recentTransactions = PointTransaction::query()
            ->latest('transacted_at')
            ->take(10)
            ->get();

        return view('dashboard.bk', compact('stats', 'zoneData', 'topStudents', 'recentTransactions'));
    }
}
