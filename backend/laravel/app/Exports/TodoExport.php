<?php

namespace App\Exports;

use App\Models\Todo;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class TodoExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $todos;

    public function __construct(Collection $todos)
    {
        $this->todos = $todos;
    }

    public function collection()
    {
        return $this->todos;
    }

    public function headings(): array
    {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority'
        ];
    }

    public function map($todo): array
    {
        return [
            $todo->title,
            $todo->assignee ? $todo->assignee->name : '',
            $todo->due_date,
            $todo->time_tracked,
            $todo->status,
            $todo->priority,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $rowCount = $this->todos->count() + 2; // +1 untuk heading, +1 karena index start 1
                $totalTime = $this->todos->sum('time_tracked');

                // Tambahkan summary row
                $sheet->setCellValue('A' . $rowCount, 'TOTAL');
                $sheet->setCellValue('D' . $rowCount, $totalTime);
                $sheet->getStyle('A' . $rowCount . ':F' . $rowCount)->getFont()->setBold(true);
            }
        ];
    }
}