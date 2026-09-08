<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Report extends Model
{
    protected $fillable = [
        'student_id',
        'reported_by',
        'verified_by',
        'violation_rule_id',
        'achievement_rule_id',
        'type',
        'occurred_on',
        'description',
        'status',
        'verification_note',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function violationRule(): BelongsTo
    {
        return $this->belongsTo(ViolationRule::class);
    }

    public function achievementRule(): BelongsTo
    {
        return $this->belongsTo(AchievementRule::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function studentCase(): HasOne
    {
        return $this->hasOne(StudentCase::class, 'report_id');
    }
}
