<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataApiTest extends TestCase
{
    use RefreshDatabase;

    private User $bkUser;

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
    }

    public function test_can_crud_school_year(): void
    {
        $this->actingAs($this->bkUser);

        // CREATE
        $response = $this->postJson('/api/school-years', [
            'name' => '2026/2027',
            'starts_on' => '2026-07-15',
            'ends_on' => '2027-06-20',
            'is_active' => true,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('school_years', ['name' => '2026/2027']);

        $id = $response->json('id');

        // READ
        $this->getJson('/api/school-years')->assertOk()->assertJsonCount(1);

        // UPDATE
        $this->putJson("/api/school-years/{$id}", [
            'name' => '2026/2027-Revisi',
            'starts_on' => '2026-07-15',
            'ends_on' => '2027-06-25',
            'is_active' => true,
        ])->assertOk();
        $this->assertDatabaseHas('school_years', ['name' => '2026/2027-Revisi']);

        // DELETE
        $this->deleteJson("/api/school-years/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('school_years', ['id' => $id]);
    }

    public function test_can_crud_school_class(): void
    {
        $this->actingAs($this->bkUser);

        $sy = SchoolYear::create([
            'name' => '2026/2027',
            'starts_on' => '2026-07-15',
            'ends_on' => '2027-06-20',
            'is_active' => true,
        ]);

        // CREATE
        $response = $this->postJson('/api/school-classes', [
            'school_year_id' => $sy->id,
            'name' => 'X-MIPA-1',
            'grade_level' => 10,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('classes', ['name' => 'X-MIPA-1']);

        $id = $response->json('id');

        // UPDATE
        $this->putJson("/api/school-classes/{$id}", [
            'school_year_id' => $sy->id,
            'name' => 'X-MIPA-2',
            'grade_level' => 10,
        ])->assertOk();
        $this->assertDatabaseHas('classes', ['name' => 'X-MIPA-2']);

        // DELETE
        $this->deleteJson("/api/school-classes/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('classes', ['id' => $id]);
    }

    public function test_can_crud_student(): void
    {
        $this->actingAs($this->bkUser);

        $sy = SchoolYear::create([
            'name' => '2026/2027',
            'starts_on' => '2026-07-15',
            'ends_on' => '2027-06-20',
            'is_active' => true,
        ]);
        $sc = SchoolClass::create(['school_year_id' => $sy->id, 'name' => 'X-MIPA-1', 'grade_level' => 10, 'major' => 'MIPA']);

        // CREATE
        $response = $this->postJson('/api/students', [
            'class_id' => $sc->id,
            'nis' => '260099',
            'nisn' => '0099887766',
            'name' => 'Budi Santoso',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('students', ['nis' => '260099', 'name' => 'Budi Santoso']);
        $this->assertDatabaseHas('users', ['username' => '260099', 'role' => 'siswa']);

        $id = $response->json('id');

        // UPDATE
        $this->putJson("/api/students/{$id}", [
            'class_id' => $sc->id,
            'nis' => '260099',
            'nisn' => '0099887766',
            'name' => 'Budi Santoso Updated',
            'gender' => 'L',
            'is_active' => true,
        ])->assertOk();
        $this->assertDatabaseHas('students', ['name' => 'Budi Santoso Updated']);

        // DELETE
        $this->deleteJson("/api/students/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('students', ['id' => $id]);
    }

    public function test_can_crud_violation_rule(): void
    {
        $this->actingAs($this->bkUser);

        // CREATE
        $response = $this->postJson('/api/violation-rules', [
            'code' => 'DIS-01',
            'name' => 'Terlambat Datang ke Sekolah',
            'points' => 10,
            'severity' => 'ringan',
            'description' => 'Terlambat masuk sekolah pagi',
            'is_active' => true,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('violation_rules', ['code' => 'DIS-01']);

        $id = $response->json('id');

        // UPDATE
        $this->putJson("/api/violation-rules/{$id}", [
            'code' => 'DIS-01',
            'name' => 'Terlambat Datang ke Sekolah (>15 Menit)',
            'points' => 15,
            'severity' => 'sedang',
            'description' => 'Terlambat lebih dari 15 menit',
            'is_active' => true,
        ])->assertOk();
        $this->assertDatabaseHas('violation_rules', ['points' => 15]);

        // DELETE
        $this->deleteJson("/api/violation-rules/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('violation_rules', ['id' => $id]);
    }

    public function test_can_crud_achievement_rule(): void
    {
        $this->actingAs($this->bkUser);

        // CREATE
        $response = $this->postJson('/api/achievement-rules', [
            'code' => 'ACH-01',
            'name' => 'Juara 1 OSN Tingkat Kota',
            'points' => 50,
            'pillar' => 'prestasi_akademik',
            'description' => 'Olimpiade Sains Nasional',
            'is_active' => true,
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('achievement_rules', ['code' => 'ACH-01']);

        $id = $response->json('id');

        // UPDATE
        $this->putJson("/api/achievement-rules/{$id}", [
            'code' => 'ACH-01',
            'name' => 'Juara 1 OSN Tingkat Provinsi',
            'points' => 100,
            'pillar' => 'prestasi_akademik',
            'description' => 'Olimpiade Sains Nasional Tingkat Provinsi',
            'is_active' => true,
        ])->assertOk();
        $this->assertDatabaseHas('achievement_rules', ['points' => 100]);

        // DELETE
        $this->deleteJson("/api/achievement-rules/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('achievement_rules', ['id' => $id]);
    }
}
