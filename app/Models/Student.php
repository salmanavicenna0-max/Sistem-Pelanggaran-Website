<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'birth_date',
        'address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(StudentCase::class);
    }

    public function getCurrentPoints(?int $schoolYearId = null): int
    {
        $query = $this->pointTransactions();
        if ($schoolYearId) {
            $query->where('school_year_id', $schoolYearId);
        }

        $last = $query->latest('transacted_at')->latest('id')->first();

        return $last ? $last->balance_after : 2000;
    }

    public function getPointZoneAttribute(): array
    {
        $points = $this->getCurrentPoints();

        return match (true) {
            $points >= 2500 => ['name' => 'Istimewa', 'color' => '#16A36A', 'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'desc' => 'Apresiasi Tertinggi'],
            $points >= 2200 => ['name' => 'Luar Biasa', 'color' => '#51C4C1', 'bg' => 'bg-teal-50 text-teal-700 border-teal-200', 'desc' => 'Anugerah Genep Waluya'],
            $points >= 1900 => ['name' => 'Normal', 'color' => '#044A87', 'bg' => 'bg-blue-50 text-blue-800 border-blue-200', 'desc' => 'Kondisi Aman / Standar'],
            $points >= 1800 => ['name' => 'Pembinaan 1', 'color' => '#E9A23B', 'bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'desc' => 'Pembinaan Wali Kelas'],
            $points >= 1700 => ['name' => 'Pembinaan 2', 'color' => '#E9A23B', 'bg' => 'bg-orange-50 text-orange-700 border-orange-200', 'desc' => 'Pembinaan Wali Kelas & BK'],
            $points >= 1600 => ['name' => 'Pembinaan 3', 'color' => '#EA580C', 'bg' => 'bg-orange-100 text-orange-800 border-orange-300', 'desc' => 'Pembinaan Tim BK + Kesiswaan'],
            $points >= 1500 => ['name' => 'Pembinaan 4', 'color' => '#D64545', 'bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'desc' => 'Panggilan Orang Tua + Kesiswaan'],
            default => ['name' => 'Intervensi Khusus', 'color' => '#991B1B', 'bg' => 'bg-red-100 text-red-800 border-red-300', 'desc' => 'Kepala Sekolah & Rapat Dewan Guru'],
        };
    }
}
