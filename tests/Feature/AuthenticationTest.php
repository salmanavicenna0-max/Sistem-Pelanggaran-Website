<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_log_in_with_username(): void
    {
        $teacher = $this->createUser(['username' => 'guru.andi', 'role' => 'guru']);

        $response = $this->post(route('login.store'), [
            'login' => 'guru.andi',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($teacher);
    }

    public function test_student_can_log_in_with_nis(): void
    {
        $studentUser = $this->createUser(['username' => 'siswa.andi', 'role' => 'siswa']);
        Student::query()->create([
            'user_id' => $studentUser->id,
            'nis' => '260001',
            'nisn' => '0101000001',
            'name' => 'Andi Pratama',
            'is_active' => true,
        ]);

        $response = $this->post(route('login.store'), [
            'login' => '260001',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($studentUser);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $this->createUser(['username' => 'nonaktif', 'is_active' => false]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => 'nonaktif',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'))->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_first_login_requires_password_change(): void
    {
        $user = $this->createUser(['is_first_login' => true]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('password.change.edit'));

        $this->actingAs($user)
            ->get(route('teacher.dashboard'))
            ->assertRedirect(route('password.change.edit'));
    }

    public function test_first_login_cannot_submit_other_passwords(): void
    {
        $user = $this->createUser(['is_first_login' => true]);

        $response = $this->actingAs($user)->put(route('password.change.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue($user->fresh()->is_first_login);
    }

    public function test_password_change_removes_first_login_requirement(): void
    {
        $user = $this->createUser(['is_first_login' => true]);

        $response = $this->actingAs($user)->put(route('password.change.update'), [
            'current_password' => 'password',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_first_login' => false]);
        $this->assertTrue(Hash::check('password-baru', $user->fresh()->password));

        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_role_middleware_blocks_wrong_role(): void
    {
        $student = $this->createUser(['role' => 'siswa']);

        $this->actingAs($student)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_homeroom_middleware_allows_assigned_teacher(): void
    {
        $teacher = $this->createUser(['role' => 'guru']);
        $schoolYearId = \DB::table('school_years')->insertGetId([
            'name' => '2026/2027',
            'starts_on' => '2026-07-01',
            'ends_on' => '2027-06-30',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        SchoolClass::query()->create([
            'school_year_id' => $schoolYearId,
            'homeroom_teacher_id' => $teacher->id,
            'name' => 'X IPA 1',
            'grade_level' => 10,
        ]);

        $this->actingAs($teacher)
            ->get(route('homeroom.dashboard'))
            ->assertOk();
    }

    private function createUser(array $attributes = []): User
    {
        return User::factory()->create([
            'username' => fake()->unique()->userName(),
            'role' => 'guru',
            'is_active' => true,
            'is_first_login' => false,
            ...$attributes,
        ]);
    }
}
