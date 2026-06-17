<?php

namespace App\Exports\Sheets;

use App\Models\LogbookModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KehadiranSheet implements
    FromCollection, WithHeadings, WithStyles, WithTitle,
    ShouldAutoSize, WithEvents
{
    private int $rowCount = 0;

    public function __construct(private int $tahun) {}

    public function collection()
    {
        $rows = LogbookModel::with(['peserta', 'sesiPelatihan.pelatihan'])
            ->whereHas('sesiPelatihan', fn($q) => $q->whereYear('tanggal', $this->tahun))
            ->orderBy((new LogbookModel)->getKeyName())
            ->get();

        $this->rowCount = $rows->count();

        return $rows->map(function ($l, $i) {
            $tanggal = $l->sesiPelatihan?->tanggal
                ? Carbon::parse($l->sesiPelatihan->tanggal)->format('d/m/Y')
                : '-';

            return [
                $i + 1,
                $l->peserta?->nama ?? '-',
                $l->sesiPelatihan?->pelatihan?->nama_pelatihan ?? '-',
                $l->sesiPelatihan?->judul_sesi ?? '-',
                $tanggal,
                ucfirst($l->status ?? '-'),
                $l->catatan ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Peserta', 'Pelatihan', 'Judul Sesi', 'Tanggal', 'Status', 'Catatan'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1565C0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 1;
                $lastCol = 'G';

                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD0D0D0'],
                        ],
                    ],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFF0F4FF']],
                        ]);
                    }

                    // Warna status kehadiran
                    $status = strtolower($sheet->getCell("F{$row}")->getValue());
                    $color  = match ($status) {
                        'hadir'  => 'FFE8F5E9',
                        'sakit'  => 'FFFFF8E1',
                        'izin'   => 'FFFFF8E1',
                        'absen'  => 'FFFFEBEE',
                        default  => 'FFFFFFFF',
                    };
                    $sheet->getStyle("F{$row}")->applyFromArray([
                        'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $color]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->freezePane('A2');
                $sheet->getRowDimension(1)->setRowHeight(22);

                $totalRow = $lastRow + 2;
                $hadir    = $this->rowCount > 0
                    ? LogbookModel::whereHas('sesiPelatihan', fn($q) => $q->whereYear('tanggal', $this->tahun))
                        ->where('status', 'hadir')->count()
                    : 0;
                $persen   = $this->rowCount > 0
                    ? round(($hadir / $this->rowCount) * 100, 1)
                    : 0;

                $sheet->setCellValue("A{$totalRow}", "Total Sesi Tercatat: {$this->rowCount}");
                $sheet->setCellValue("D{$totalRow}", "Hadir: {$hadir}");
                $sheet->setCellValue("E{$totalRow}", "Rata-rata Kehadiran: {$persen}%");
                $sheet->getStyle("A{$totalRow}:E{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                $infoRow = $totalRow + 1;
                $sheet->setCellValue("A{$infoRow}", 'Dicetak pada: ' . now()->translatedFormat('j F Y, H:i'));
                $sheet->getStyle("A{$infoRow}")->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['argb' => 'FF888888'], 'size' => 9],
                ]);
            },
        ];
    }

    public function title(): string
    {
        return 'Kehadiran';
    }
}