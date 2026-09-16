<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\StudentsImport;

class StudentController extends Controller
{
    public function index(): JsonResponse
    {
        $students = Student::with(['user', 'schoolClass'])->get();

        return response()->json($students);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'nis' => 'required|string|unique:students,nis',
            'nisn' => 'nullable|string|unique:students,nisn',
            'name' => 'required|string|max:150',
            'gender' => 'required|in:L,P,male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $gender = in_array($validated['gender'], ['L', 'male']) ? 'male' : 'female';

        $student = DB::transaction(function () use ($validated, $gender) {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $validated['nis'], // Use NIS as username for students
                'email' => $validated['nis'].'@student.sman6bdg.sch.id',
                'password' => Hash::make($validated['nis']), // Default password is NIS
                'role' => 'siswa',
                'is_active' => $validated['is_active'] ?? true,
                'is_first_login' => true,
            ]);

            return Student::create([
                'user_id' => $user->id,
                'class_id' => $validated['class_id'],
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'name' => $validated['name'],
                'gender' => $gender,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);
        });

        $student->load(['user', 'schoolClass']);

        return response()->json($student, 201);
    }

    public function show(Student $student): JsonResponse
    {
        $student->load(['user', 'schoolClass']);

        return response()->json($student);
    }

    public function update(Request $request, Student $student): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'nis' => ['required', 'string', Rule::unique('students')->ignore($student->id)],
            'nisn' => ['nullable', 'string', Rule::unique('students')->ignore($student->id)],
            'name' => 'required|string|max:150',
            'gender' => 'required|in:L,P,male,female',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $gender = in_array($validated['gender'], ['L', 'male']) ? 'male' : 'female';

        DB::transaction(function () use ($student, $validated, $gender) {
            $student->update([
                'class_id' => $validated['class_id'],
                'nis' => $validated['nis'],
                'nisn' => $validated['nisn'] ?? null,
                'name' => $validated['name'],
                'gender' => $gender,
                'birth_date' => $validated['birth_date'] ?? null,
                'address' => $validated['address'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if ($student->user) {
                $student->user->update([
                    'name' => $validated['name'],
                    'username' => $validated['nis'],
                    'is_active' => $validated['is_active'] ?? true,
                ]);
            }
        });

        $student->load(['user', 'schoolClass']);

        return response()->json($student);
    }

    public function destroy(Student $student): JsonResponse
    {
        DB::transaction(function () use ($student) {
            $student->delete();
            $student->user()->delete();
        });

        return response()->json(null, 204);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return response()->json(['message' => 'Data siswa berhasil diimpor.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengimpor data: ' . $e->getMessage()], 422);
        }
    }

    public function downloadTemplate()
    {
        $csvContent = "nama_lengkap,nis,nisn,jenis_kelamin,tanggal_lahir,alamat,nama_kelas\nJohn Doe,1001,001001001,L,2005-12-31,Jl. Contoh 1,X IPA 1\nJane Doe,1002,,P,2006-01-01,,X IPS 2";
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_siswa.csv"');
    }
}
