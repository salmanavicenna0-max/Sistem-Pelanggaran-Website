<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseAction extends Model
{
    protected $fillable = [
        'case_id',
        'performed_by',
        'action',
        'note',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'datetime',
        ];
    }

    public function studentCase(): BelongsTo
    {
        return $this->belongsTo(StudentCase::class, 'case_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
