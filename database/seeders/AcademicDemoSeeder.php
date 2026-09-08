<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AcademicDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('school_years')->updateOrInsert(
            ['name' => '2026/2027'],
            [
                'starts_on' => '2026-07-01',
                'ends_on' => '2027-06-30',
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ],
        );

        $schoolYearId = DB::table('school_years')->where('name', '2026/2027')->value('id');

        $users = [
            [
                'name' => 'Admin Kesiswaan',
                'username' => 'kesiswaan',
                'nip' => '198501012010011001',
                'email' => 'kesiswaan@example.test',
                'role' => 'kesiswaan_bk',
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'guru.budi',
                'nip' => '198602022011011002',
                'email' => 'budi.santoso@example.test',
                'role' => 'guru',
            ],
            [
                'name' => 'Siti Aminah',
                'username' => 'guru.siti',
                'nip' => '198703032012012003',
                'email' => 'siti.aminah@example.test',
                'role' => 'guru',
            ],
            [
                'name' => 'Andi Pratama',
                'username' => 'andi.pratama',
                'nip' => null,
                'email' => 'andi.pratama@example.test',
                'role' => 'siswa',
            ],
            [
                'name' => 'Citra Lestari',
                'username' => 'citra.lestari',
                'nip' => null,
                'email' => 'citra.lestari@example.test',
                'role' => 'siswa',
            ],
            [
                'name' => 'Dimas Saputra',
                'username' => 'dimas.saputra',
                'nip' => null,
                'email' => 'dimas.saputra@example.test',
                'role' => 'siswa',
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['username' => $user['username']],
                [
                    ...$user,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'is_first_login' => false,
                    'email_verified_at' => $now,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        $teacherIds = DB::table('users')
            ->whereIn('username', ['guru.budi', 'guru.siti'])
            ->pluck('id', 'username');

        $classes = [
            ['name' => 'X IPA 1', 'grade_level' => 10, 'homeroom_teacher_id' => $teacherIds['guru.budi']],
            ['name' => 'X IPA 2', 'grade_level' => 10, 'homeroom_teacher_id' => $teacherIds['guru.siti']],
            ['name' => 'XI IPA 1', 'grade_level' => 11, 'homeroom_teacher_id' => $teacherIds['guru.budi']],
        ];

        foreach ($classes as $class) {
            DB::table('classes')->updateOrInsert(
                ['school_year_id' => $schoolYearId, 'name' => $class['name']],
                [
                    ...$class,
                    'school_year_id' => $schoolYearId,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        $classIds = DB::table('classes')
            ->where('school_year_id', $schoolYearId)
            ->pluck('id', 'name');

        $studentUsers = DB::table('users')
            ->whereIn('username', ['andi.pratama', 'citra.lestari', 'dimas.saputra'])
            ->pluck('id', 'username');

        $students = [
            ['username' => 'andi.pratama', 'nis' => '260001', 'nisn' => '0101000001', 'class_name' => 'X IPA 1', 'gender' => 'male'],
            ['username' => 'citra.lestari', 'nis' => '260002', 'nisn' => '0101000002', 'class_name' => 'X IPA 1', 'gender' => 'female'],
            ['username' => 'dimas.saputra', 'nis' => '260003', 'nisn' => '0101000003', 'class_name' => 'X IPA 2', 'gender' => 'male'],
        ];

        foreach ($students as $student) {
            DB::table('students')->updateOrInsert(
                ['nis' => $student['nis']],
                [
                    'user_id' => $studentUsers[$student['username']],
                    'class_id' => $classIds[$student['class_name']],
                    'nis' => $student['nis'],
                    'nisn' => $student['nisn'],
                    'name' => DB::table('users')->where('id', $studentUsers[$student['username']])->value('name'),
                    'gender' => $student['gender'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }
}
