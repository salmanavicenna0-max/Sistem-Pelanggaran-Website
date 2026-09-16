<?php

namespace App\Exports;

use App\Models\PointTransaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PointTransactionsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query(): \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation
    {
        return PointTransaction::query()->with(['student.user', 'student.schoolClass', 'reporter'])->orderBy('transacted_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Tanggal Transaksi',
            'Siswa',
            'NIS',
            'Kelas',
            'Tipe',
            'Poin',
            'Deskripsi',
            'Dicatat Oleh'
        ];
    }

    public function map($transaction): array
    {
        return [
            $transaction->transacted_at->format('d/m/Y H:i'),
            $transaction->student->name,
            $transaction->student->nis,
            $transaction->student->schoolClass->name ?? '-',
            $transaction->type === 'violation' ? 'Pelanggaran' : 'Prestasi',
            $transaction->type === 'violation' ? '-' . $transaction->points : '+' . $transaction->points,
            $transaction->description,
            $transaction->reporter->name ?? 'Sistem'
        ];
    }
}
