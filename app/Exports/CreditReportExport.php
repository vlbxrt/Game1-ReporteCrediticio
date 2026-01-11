<?php

namespace App\Exports;

use App\Models\SubscriptionReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class CreditReportExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithChunkReading,
    ShouldQueue
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to   = $to;
    }

    public function query()
    {
        return SubscriptionReport::query()
            ->with(['subscription', 'loans', 'otherDebts', 'creditCards'])
            ->whereBetween('created_at', [$this->from, $this->to]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'DNI',
            'Email',
            'Teléfono',
            'Compañía',
            'Tipo de deuda',
            'Situación',
            'Atraso',
            'Entidad',
            'Monto total',
            'Línea total',
            'Línea usada',
            'Reporte subido el',
            'Estado'
        ];
    }

    public function map($report): array
    {
        $rows = [];

        // Prestamo
        foreach ($report->loans as $loan) {
            $rows[] = [
                $report->id,
                $report->subscription->full_name,
                $report->subscription->document,
                $report->subscription->email,
                $report->subscription->phone,
                $loan->bank,
                'Préstamo',
                $loan->status,
                $loan->expiration_days,
                $loan->bank,
                $loan->amount,
                null,
                null,
                $report->created_at->format('Y-m-d'),
                'ACTIVO'
            ];
        }

        // Otras deudas
        foreach ($report->otherDebts as $debt) {
            $rows[] = [
                $report->id,
                $report->subscription->full_name,
                $report->subscription->document,
                $report->subscription->email,
                $report->subscription->phone,
                $debt->entity,
                'Otra deuda',
                'NOR',
                $debt->expiration_days,
                $debt->entity,
                $debt->amount,
                null,
                null,
                $report->created_at->format('Y-m-d'),
                'ACTIVO'
            ];
        }

        // Tarjetas 
        foreach ($report->creditCards as $card) {
            $rows[] = [
                $report->id,
                $report->subscription->full_name,
                $report->subscription->document,
                $report->subscription->email,
                $report->subscription->phone,
                $card->bank,
                'Tarjeta de crédito',
                'NOR',
                0,
                $card->bank,
                null,
                $card->line,
                $card->used,
                $report->created_at->format('Y-m-d'),
                'ACTIVO'
            ];
        }

        return $rows;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
