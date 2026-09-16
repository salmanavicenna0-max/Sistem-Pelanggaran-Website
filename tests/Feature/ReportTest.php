<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $bkUser;

    private User $studentUser;

    private Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bkUser = User::create([
            'name' => 'Staff BK',
            'username' => 'bk.staff',
            'email' => 'bk@sman6bdg.sch.id',
            'password' => bcrypt('password'),
            'role' => 'kesiswaan_bk',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $year = SchoolYear::create([
            'name' => '2026/2027',
            'starts_on' => '2026-07-15',
            'ends_on' => '2027-06-20',
            'is_active' => true,
        ]);

        $class = SchoolClass::create([
            'school_year_id' => $year->id,
            'name' => 'X-MIPA-1',
            'grade_level' => '10',
            'department' => 'MIPA',
        ]);

        $this->studentUser = User::create([
            'name' => 'Budi Santoso',
            'username' => '1026001',
            'email' => 'budi@siswa.sman6bdg.sch.id',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
            'password_changed_at' => now(),
        ]);

        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'class_id' => $class->id,
            'name' => 'Budi Santoso',
            'nis' => '1026001',
            'nisn' => '0061234567',
            'gender' => 'male',
            'is_active' => true,
        ]);
    }

    public function test_can_view_create_report_page(): void
    {
        $this->actingAs($this->bkUser);

        $response = $this->get('/lapor');

        $response->assertOk();
        $response->assertSee('Formulir Pelaporan Kejadian');
    }

    public function test_can_submit_violation_report(): void
    {
        $this->actingAs($this->bkUser);

        $rule = ViolationRule::create([
            'code' => 'KLR-01',
            'name' => 'Terlambat Masuk Sekolah',
            'severity' => 'ringan',
            'points' => 5,
            'is_active' => true,
        ]);

        $response = $this->post('/lapor', [
            'student_nis' => $this->student->nis,
            'type' => 'violation',
            'violation_rule_id' => $rule->id,
            'occurred_on' => '2026-09-10',
            'description' => 'Siswa datang terlambat 15 menit ke kelas.',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('reports', [
            'student_id' => $this->student->id,
            'type' => 'violation',
            'status' => 'pending',
        ]);
    }

    public function test_can_view_points_and_cases_pages(): void
    {
        $this->actingAs($this->bkUser);

        $this->get('/poin')->assertOk();
        $this->get('/reports')->assertOk();
        $this->get('/kasus')->assertOk();

        // Acting as student
        $this->actingAs($this->studentUser);
        $this->get('/poin')->assertOk();
        $this->get('/lapor')->assertOk();
    }
}
