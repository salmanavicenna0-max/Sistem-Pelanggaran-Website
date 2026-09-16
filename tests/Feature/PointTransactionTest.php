<?php

namespace Tests\Feature;

use App\Models\PointTransaction;
use App\Models\Report;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationRule;
use App\Services\PointTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointTransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $bkUser;

    private Student $student;

    private SchoolYear $schoolYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bkUser = User::factory()->create(['role' => 'kesiswaan_bk', 'is_active' => true]);

        $this->schoolYear = SchoolYear::create([
            'name' => '2025/2026',
            'starts_on' => '2025-07-15',
            'ends_on' => '2026-06-15',
            'is_active' => true,
        ]);

        $schoolClass = SchoolClass::create([
            'school_year_id' => $this->schoolYear->id,
            'name' => 'XII IPA 1',
            'grade_level' => 12,
        ]);

        $userSiswa = User::factory()->create(['name' => 'Siswa Test', 'role' => 'siswa', 'is_active' => true]);
        $this->student = Student::create([
            'user_id' => $userSiswa->id,
            'class_id' => $schoolClass->id,
            'nis' => '123456',
            'nisn' => '1234567890',
            'name' => 'Siswa Test',
            'gender' => 'male',
            'birth_date' => '2005-01-01',
            'is_active' => true,
        ]);

        // Initial point transaction (simulated)
        PointTransaction::create([
            'student_id' => $this->student->id,
            'school_year_id' => $this->schoolYear->id,
            'type' => 'initial_balance',
            'points' => 2000,
            'balance_before' => 0,
            'balance_after' => 2000,
            'transacted_at' => now(),
        ]);
    }

    public function test_violation_report_deducts_points(): void
    {
        $rule = ViolationRule::create([
            'code' => 'V01',
            'name' => 'Terlambat',
            'category' => 'Kedisiplinan',
            'severity' => 'ringan',
            'points' => 50,
            'is_active' => true,
        ]);

        $report = Report::create([
            'student_id' => $this->student->id,
            'reported_by' => User::factory()->create(['role' => 'guru'])->id,
            'type' => 'violation',
            'violation_rule_id' => $rule->id,
            'status' => 'pending',
            'occurred_on' => now(),
            'description' => 'Terlambat 15 menit',
        ]);

        $response = $this->actingAs($this->bkUser)->put(route('reports.verify', $report), [
            'status' => 'approved',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(1950, $this->student->getCurrentPoints());

        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $this->student->id,
            'type' => 'violation',
            'points' => 50,
            'balance_after' => 1950,
        ]);
    }

    public function test_bk_can_add_manual_achievement(): void
    {
        $response = $this->actingAs($this->bkUser)->post(route('points.manual-achievement'), [
            'student_id' => $this->student->id,
            'points' => 100,
            'description' => 'Juara 1 Lomba OSN',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(2100, $this->student->getCurrentPoints());
        $this->assertDatabaseHas('point_transactions', [
            'student_id' => $this->student->id,
            'type' => 'achievement',
            'points' => 100,
            'balance_after' => 2100,
            'description' => 'Penghargaan Manual: Juara 1 Lomba OSN',
        ]);
    }

    public function test_bk_can_correct_points(): void
    {
        $response = $this->actingAs($this->bkUser)->post(route('points.correction'), [
            'student_id' => $this->student->id,
            'target_balance' => 2500,
            'reason' => 'Penyesuaian migrasi data',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(2500, $this->student->getCurrentPoints());
        $this->assertDatabaseHas('point_transactions', [
            'type' => 'correction',
            'points' => 500,
            'balance_after' => 2500,
        ]);
    }

    public function test_bk_can_reverse_transaction(): void
    {
        // Add manual achievement first
        $transaction = app(PointTransactionService::class)->recordManualAchievement(
            $this->student, 100, 'Salah input', null, $this->bkUser->id
        );

        $this->assertEquals(2100, $this->student->getCurrentPoints());

        // Reverse it
        $response = $this->actingAs($this->bkUser)->post(route('points.reversal', $transaction), [
            'reason' => 'Batal, salah siswa',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(2000, $this->student->getCurrentPoints());
        $this->assertDatabaseHas('point_transactions', [
            'type' => 'reversal',
            'reference_id' => $transaction->id,
            'balance_after' => 2000,
        ]);
    }

    public function test_student_can_view_handbook_via_api(): void
    {
        $response = $this->actingAs($this->student->user)->getJson('/api/student/handbook');

        $response->assertOk()
            ->assertJsonPath('student.nis', $this->student->nis)
            ->assertJsonPath('points.current_balance', 2000);
    }
}
