<?php

use App\Http\Controllers\Api\AchievementRuleController;
use App\Http\Controllers\Api\CaseController as ApiCaseController;
use App\Http\Controllers\Api\PointTransactionController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SchoolYearController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ViolationRuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum,web', 'active'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API for Student (Mobile & Web)
    Route::get('student/handbook', [PointTransactionController::class, 'studentHandbook']);
    Route::get('student/transactions', [PointTransactionController::class, 'studentTransactions']);

    // Master Data & Actions - Accessible by BK / Kesiswaan
    Route::middleware(['role:kesiswaan_bk'])->group(function () {
        Route::apiResource('school-years', SchoolYearController::class);
        Route::apiResource('school-classes', SchoolClassController::class);
        Route::post('students/import', [StudentController::class, 'import']);
        Route::get('students/import/template', [StudentController::class, 'downloadTemplate']);
        Route::apiResource('students', StudentController::class);
        
        Route::post('teachers/import', [TeacherController::class, 'import']);
        Route::get('teachers/import/template', [TeacherController::class, 'downloadTemplate']);
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('violation-rules', ViolationRuleController::class);
        Route::apiResource('achievement-rules', AchievementRuleController::class);

        // Point Actions
        Route::post('points/manual-achievement', [PointTransactionController::class, 'manualAchievement']);
        Route::post('points/correction', [PointTransactionController::class, 'correction']);
        Route::post('points/{transaction}/reversal', [PointTransactionController::class, 'reversal']);

        // Case & Pembinaan Actions
        Route::get('cases', [ApiCaseController::class, 'index']);
        Route::post('cases', [ApiCaseController::class, 'store']);
        Route::get('cases/{case}', [ApiCaseController::class, 'show']);
        Route::post('cases/{case}/actions', [ApiCaseController::class, 'storeAction']);
    });
});
