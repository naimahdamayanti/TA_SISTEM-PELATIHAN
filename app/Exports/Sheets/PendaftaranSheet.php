<?php

namespace App\Exports\Sheets;

use App\Models\PendaftaranModel;
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

class PendaftaranSheet implements
    FromCollection, WithHeadings, WithStyles, WithTitle,
    ShouldAutoSize, WithEvents
{
    private int $rowCount = 0;

    public function __construct(private int $tahun) {}

    public function collection()
    {
        $rows = PendaftaranModel::with('pelatihan')
            ->whereYear('tgl_daftar', $this->tahun)
            ->orderBy('tgl_daftar')
            ->get();

        $this->rowCount = $rows->count();

        return $rows->map(function ($p, $i) {
            $nama = trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? ''));

            return [
                $i + 1,
                $nama ?: '-',
                $p->email ?? '-',
                $p->perusahaan ?? '-',
                $p->pelatihan?->nama_pelatihan ?? '-',
                $p->tgl_daftar ? Carbon::parse($p->tgl_daftar)->format('d/m/Y') : '-',
                ucfirst($p->status ?? '-'),
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Pendaftar', 'Email', 'Perusahaan', 'Pelatihan', 'Tgl Daftar', 'Status'];
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
                $sheet    = $event->sheet->getDelegate();
                $lastRow  = $this->rowCount + 1;
                $lastCol  = 'G';

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
                }

                // Warna badge status
                for ($row = 2; $row <= $lastRow; $row++) {
                    $status = strtolower($sheet->getCell("G{$row}")->getValue());
                    $color  = match ($status) {
                        'diterima'  => 'FFE8F5E9',
                        'menunggu'  => 'FFFFF8E1',
                        'ditolak'   => 'FFFFEBEE',
                        default     => 'FFFFFFFF',
                    };
                    $sheet->getStyle("G{$row}")->applyFromArray([
                        'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $color]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->freezePane('A2');
                $sheet->getRowDimension(1)->setRowHeight(22);

                $totalRow   = $lastRow + 2;
                $diterima   = PendaftaranModel::whereYear('tgl_daftar', $this->tahun)->where('status', 'diterima')->count();
                $menunggu   = PendaftaranModel::whereYear('tgl_daftar', $this->tahun)->where('status', 'menunggu')->count();
                $ditolak    = PendaftaranModel::whereYear('tgl_daftar', $this->tahun)->where('status', 'ditolak')->count();

                $sheet->setCellValue("A{$totalRow}", 'Ringkasan');
                $sheet->setCellValue("B{$totalRow}", "Diterima: {$diterima}");
                $sheet->setCellValue("C{$totalRow}", "Menunggu: {$menunggu}");
                $sheet->setCellValue("D{$totalRow}", "Ditolak: {$ditolak}");
                $sheet->getStyle("A{$totalRow}:D{$totalRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10],
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
        return 'Pendaftaran';
    }
}