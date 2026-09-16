<?php

namespace App\Services;

use App\Models\AchievementRule;
use App\Models\PointTransaction;
use App\Models\Report;
use App\Models\SchoolYear;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class PointTransactionService
{
    protected function getActiveSchoolYearId(): ?int
    {
        return SchoolYear::where('is_active', true)->value('id');
    }

    public function recordReportApproval(Report $report, int $performedBy): PointTransaction
    {
        return DB::transaction(function () use ($report, $performedBy) {
            $student = $report->student;
            $balanceBefore = $student->getCurrentPoints();

            if ($report->type === 'achievement') {
                $points = $report->achievementRule->points;
                $balanceAfter = $balanceBefore + $points;
                $description = 'Prestasi: '.$report->achievementRule->name;
            } else {
                $points = $report->violationRule->points;
                $balanceAfter = $balanceBefore - $points;
                $description = 'Pelanggaran: '.$report->violationRule->name;
            }

            return PointTransaction::create([
                'student_id' => $report->student_id,
                'school_year_id' => $this->getActiveSchoolYearId(),
                'performed_by' => $performedBy,
                'reference_type' => Report::class,
                'reference_id' => $report->id,
                'type' => $report->type,
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $description,
                'transacted_at' => now(),
            ]);
        });
    }

    public function recordManualAchievement(Student $student, int $points, string $description, ?int $ruleId, int $performedBy): PointTransaction
    {
        return DB::transaction(function () use ($student, $points, $description, $ruleId, $performedBy) {
            $balanceBefore = $student->getCurrentPoints();
            $balanceAfter = $balanceBefore + $points;

            return PointTransaction::create([
                'student_id' => $student->id,
                'school_year_id' => $this->getActiveSchoolYearId(),
                'performed_by' => $performedBy,
                'reference_type' => $ruleId ? AchievementRule::class : null,
                'reference_id' => $ruleId,
                'type' => 'achievement',
                'points' => $points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => 'Penghargaan Manual: '.$description,
                'transacted_at' => now(),
            ]);
        });
    }

    public function recordCorrection(Student $student, int $targetBalance, string $reason, int $performedBy): PointTransaction
    {
        return DB::transaction(function () use ($student, $targetBalance, $reason, $performedBy) {
            $balanceBefore = $student->getCurrentPoints();
            $pointsDelta = $targetBalance - $balanceBefore;

            return PointTransaction::create([
                'student_id' => $student->id,
                'school_year_id' => $this->getActiveSchoolYearId(),
                'performed_by' => $performedBy,
                'reference_type' => null,
                'reference_id' => null,
                'type' => 'correction',
                'points' => abs($pointsDelta),
                'balance_before' => $balanceBefore,
                'balance_after' => $targetBalance,
                'description' => 'Koreksi Saldo: '.$reason,
                'transacted_at' => now(),
            ]);
        });
    }

    public function recordReversal(PointTransaction $originalTransaction, string $reason, int $performedBy): PointTransaction
    {
        return DB::transaction(function () use ($originalTransaction, $reason, $performedBy) {
            $student = $originalTransaction->student;
            $balanceBefore = $student->getCurrentPoints();

            $effect = $originalTransaction->balance_after - $originalTransaction->balance_before;
            $balanceAfter = $balanceBefore - $effect;

            return PointTransaction::create([
                'student_id' => $student->id,
                'school_year_id' => $this->getActiveSchoolYearId(),
                'performed_by' => $performedBy,
                'reference_type' => PointTransaction::class,
                'reference_id' => $originalTransaction->id,
                'type' => 'reversal',
                'points' => $originalTransaction->points,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => "Pembatalan Transaksi #{$originalTransaction->id}: ".$reason,
                'transacted_at' => now(),
            ]);
        });
    }
}
