<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                if (empty($row['username']) || empty($row['nama_lengkap'])) {
                    continue;
                }

                $username = $row['username'];
                if (User::where('username', $username)->exists()) {
                    throw new \Exception("Username '{$username}' sudah terdaftar.");
                }

                User::create([
                    'name' => $row['nama_lengkap'],
                    'username' => $username,
                    'nip' => $row['nip'] ?? null,
                    'email' => $username.'@guru.sman6bdg.sch.id',
                    'password' => Hash::make($username),
                    'role' => 'guru',
                    'is_active' => true,
                    'is_first_login' => true,
                ]);
            }
        });
    }
}
