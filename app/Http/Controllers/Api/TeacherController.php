<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TeachersImport;

class TeacherController extends Controller
{
    public function index(): JsonResponse
    {
        $teachers = User::where('role', 'guru')->get();

        return response()->json($teachers);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|unique:users,username',
            'nip' => 'nullable|string|unique:users,nip',
            'is_active' => 'boolean',
        ]);

        $teacher = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'nip' => $validated['nip'] ?? null,
            'email' => $validated['username'].'@guru.sman6bdg.sch.id',
            'password' => Hash::make($validated['username']), // default password
            'role' => 'guru',
            'is_active' => $validated['is_active'] ?? true,
            'is_first_login' => true,
        ]);

        return response()->json($teacher, 201);
    }

    public function show(User $teacher): JsonResponse
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        return response()->json($teacher);
    }

    public function update(Request $request, User $teacher): JsonResponse
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => ['required', 'string', Rule::unique('users')->ignore($teacher->id)],
            'nip' => ['nullable', 'string', Rule::unique('users')->ignore($teacher->id)],
            'is_active' => 'boolean',
        ]);

        $teacher->update($validated);

        return response()->json($teacher);
    }

    public function destroy(User $teacher): JsonResponse
    {
        if ($teacher->role !== 'guru') {
            abort(404);
        }
        $teacher->delete();

        return response()->json(null, 204);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new TeachersImport, $request->file('file'));
            return response()->json(['message' => 'Data guru berhasil diimpor.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengimpor data: ' . $e->getMessage()], 422);
        }
    }

    public function downloadTemplate()
    {
        $csvContent = "nama_lengkap,username,nip\nBudi Santoso,budiguru,198001012005011003\nSiti Aminah,sitiguru,";
        
        return response($csvContent)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template_guru.csv"');
    }
}
