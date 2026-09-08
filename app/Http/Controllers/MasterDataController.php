<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Models\SchoolYear;
use App\Models\SchoolClass;
use App\Models\ViolationRule;
use App\Models\AchievementRule;
use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MasterDataController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'active', 'password.changed']);
    }

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
}