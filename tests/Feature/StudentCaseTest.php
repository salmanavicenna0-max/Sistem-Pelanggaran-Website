<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\StudentCase;
use App\Models\User;
use App\Models\ViolationRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCaseTest extends TestCase
{
    use RefreshDatabase;

    private User $bkUser;

    private User $siswaUser;

    private Student $student;

    private Report $report;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bkUser = User::factory()->create(['role' => 'kesiswaan_bk', 'is_active' => true]);
        $this->siswaUser = User::factory()->create(['role' => 'siswa', 'is_active' => true]);

        $schoolYear = SchoolYear::create([
            'name' => '2025/2026',
            'starts_on' => '2025-07-15',
            'ends_on' => '2026-06-15',
            'is_active' => true,
        ]);

        $schoolClass = SchoolClass::create([
            'school_year_id' => $schoolYear->id,
            'name' => 'XI IPA 2',
            'grade_level' => 11,
        ]);

        $this->student = Student::create([
            'user_id' => $this->siswaUser->id,
            'class_id' => $schoolClass->id,
            'nis' => '998877',
            'nisn' => '9988776655',
            'name' => 'Siswa Kasus Test',
            'gender' => 'male',
            'birth_date' => '2006-03-12',
            'is_active' => true,
        ]);

        $rule = ViolationRule::create([
            'code' => 'B.26',
            'name' => 'Perilaku Bullying',
            'category' => 'Sikap',
            'severity' => 'sedang',
            'points' => 32,
        ]);

        $this->report = Report::create([
            'student_id' => $this->student->id,
            'reported_by' => $this->bkUser->id,
            'type' => 'violation',
            'violation_rule_id' => $rule->id,
            'occurred_on' => '2026-09-08',
            'description' => 'Terjadi perkelahian di area kantin.',
            'status' => 'approved',
        ]);
    }

    public function test_bk_can_create_case_manually(): void
    {
        $response = $this->actingAs($this->bkUser)
            ->post(route('cases.store'), [
                'student_id' => $this->student->id,
                'title' => 'Pembinaan Kedisiplinan Terlambat',
                'description' => 'Siswa terlambat 3 kali berturut-turut.',
                'confidentiality' => 'normal',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cases', [
            'student_id' => $this->student->id,
            'title' => 'Pembinaan Kedisiplinan Terlambat',
            'status' => 'open',
            'confidentiality' => 'normal',
            'report_id' => null,
        ]);
    }

    public function test_bk_can_create_case_from_report(): void
    {
        $response = $this->actingAs($this->bkUser)
            ->post(route('cases.store'), [
                'student_id' => $this->student->id,
                'report_id' => $this->report->id,
                'title' => 'Tindak Lanjut Bullying Kantin',
                'description' => 'Pertemuan khusus dengan tim BK.',
                'confidentiality' => 'confidential',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cases', [
            'student_id' => $this->student->id,
            'report_id' => $this->report->id,
            'title' => 'Tindak Lanjut Bullying Kantin',
            'status' => 'open',
            'confidentiality' => 'confidential',
        ]);
    }

    public function test_cannot_create_duplicate_case_for_same_report(): void
    {
        // First case created from report
        StudentCase::create([
            'student_id' => $this->student->id,
            'report_id' => $this->report->id,
            'created_by' => $this->bkUser->id,
            'title' => 'Kasus Pertama',
            'description' => 'Deskripsi pertama',
            'status' => 'open',
            'confidentiality' => 'normal',
        ]);

        // Attempt second case for same report
        $response = $this->actingAs($this->bkUser)
            ->post(route('cases.store'), [
                'student_id' => $this->student->id,
                'report_id' => $this->report->id,
                'title' => 'Kasus Kedua',
                'description' => 'Deskripsi kedua',
                'confidentiality' => 'normal',
            ]);

        $response->assertSessionHasErrors('report_id');
    }

    public function test_bk_can_add_action_and_update_status(): void
    {
        $case = StudentCase::create([
            'student_id' => $this->student->id,
            'created_by' => $this->bkUser->id,
            'title' => 'Kasus Penanganan',
            'description' => 'Proses pembinaan bertahap',
            'status' => 'open',
            'confidentiality' => 'normal',
        ]);

        // Action 1: In Progress
        $response1 = $this->actingAs($this->bkUser)
            ->post(route('cases.action', $case), [
                'action' => 'Konseling Individu Tahap 1',
                'note' => 'Siswa menyadari kesalahannya.',
                'status' => 'in_progress',
            ]);

        $response1->assertRedirect();
        $case->refresh();
        $this->assertEquals('in_progress', $case->status);
        $this->assertNull($case->closed_at);
        $this->assertDatabaseHas('case_actions', [
            'case_id' => $case->id,
            'action' => 'Konseling Individu Tahap 1',
        ]);

        // Action 2: Closed
        $response2 = $this->actingAs($this->bkUser)
            ->post(route('cases.action', $case), [
                'action' => 'Evaluasi Akhir dan Penyelesaian Kasus',
                'note' => 'Perilaku membaik dan tidak ada pelanggaran berulang.',
                'status' => 'closed',
            ]);

        $response2->assertRedirect();
        $case->refresh();
        $this->assertEquals('closed', $case->status);
        $this->assertNotNull($case->closed_at);
    }

    public function test_siswa_cannot_access_cases(): void
    {
        $response = $this->actingAs($this->siswaUser)
            ->get(route('cases.index'));

        $response->assertStatus(403);
    }

    public function test_api_case_endpoints(): void
    {
        // 1. Create case via API
        $response = $this->actingAs($this->bkUser, 'sanctum')
            ->postJson('/api/cases', [
                'student_id' => $this->student->id,
                'title' => 'API Case Title',
                'description' => 'API Case Description',
                'confidentiality' => 'normal',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'API Case Title');

        $caseId = $response->json('data.id');

        // 2. Add action via API
        $actionResponse = $this->actingAs($this->bkUser, 'sanctum')
            ->postJson("/api/cases/{$caseId}/actions", [
                'action' => 'API Action 1',
                'note' => 'Catatan dari API',
                'status' => 'closed',
            ]);

        $actionResponse->assertStatus(201)
            ->assertJsonPath('data.action.action', 'API Action 1')
            ->assertJsonPath('data.case.status', 'closed');
    }
}
