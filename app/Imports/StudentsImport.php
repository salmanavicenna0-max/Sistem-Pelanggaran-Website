<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        if (!$activeYear) {
            throw new \Exception("Tahun ajaran aktif belum diatur.");
        }

        DB::transaction(function () use ($rows, $activeYear) {
            foreach ($rows as $row) {
                if (empty($row['nis']) || empty($row['nama_lengkap']) || empty($row['nama_kelas'])) {
                    continue;
                }

                $schoolClass = SchoolClass::where('name', $row['nama_kelas'])
                    ->where('school_year_id', $activeYear->id)
                    ->first();

                if (!$schoolClass) {
                    throw new \Exception("Kelas '{$row['nama_kelas']}' tidak ditemukan di tahun ajaran aktif.");
                }

                $nis = $row['nis'];
                // Check if already exists to avoid unique constraint violation
                if (Student::where('nis', $nis)->exists() || User::where('username', $nis)->exists()) {
                    throw new \Exception("NIS '{$nis}' sudah terdaftar.");
                }

                $gender = in_array(strtoupper(trim($row['jenis_kelamin'] ?? 'L')), ['L', 'LAKI-LAKI', 'MALE']) ? 'male' : 'female';
                
                // Handle date conversion if it's an excel serial date
                $birthDate = $row['tanggal_lahir'] ?? null;
                if (is_numeric($birthDate)) {
                    $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthDate)->format('Y-m-d');
                }
                
                $user = User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $nis,
                    'email' => $nis.'@student.sman6bdg.sch.id',
                    'password' => Hash::make($nis),
                    'role' => 'siswa',
                    'is_active' => true,
                    'is_first_login' => true,
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'class_id' => $schoolClass->id,
                    'nis' => $nis,
                    'nisn' => $row['nisn'] ?? null,
                    'name' => $row['nama_lengkap'],
                    'gender' => $gender,
                    'birth_date' => $birthDate,
                    'address' => $row['alamat'] ?? null,
                    'is_active' => true,
                ]);
            }
        });
    }
}
