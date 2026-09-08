<?php

namespace App\Http\Controllers;

use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'password.changed']);
    }

    // --- SISWA: Buku Saku & Poin Saya ---
    public function index(): View
    {
        $student = auth()->user()->student;
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

        return view('points.siswa', compact('student', 'currentPoints', 'zone', 'recentTransactions'));
    }

    // --- BK: Riwayat Poin Siswa ---
    public function riwayat(Request $request): View
    {
        $studentId = $request->student_id;

        $student = Student::with('user')->findOrFail($studentId);
        $transactions = PointTransaction::query()
            ->where('student_id', $studentId)
            ->latest('transacted_at')
            ->get();

        return view('points.bk-riwayat', compact('student', 'transactions'));
    }
}