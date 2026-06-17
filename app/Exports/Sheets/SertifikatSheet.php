<?php

namespace App\Exports\Sheets;

use App\Models\SertifikatModel;
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

class SertifikatSheet implements
    FromCollection, WithHeadings, WithStyles, WithTitle,
    ShouldAutoSize, WithEvents
{
    private int $rowCount = 0;

    public function __construct(private int $tahun) {}

    public function collection()
    {
        $rows = SertifikatModel::with(['pendaftaran.pelatihan'])
            ->whereYear('tgl_terbit', $this->tahun)
            ->orderBy('tgl_terbit')
            ->get();

        $this->rowCount = $rows->count();

        return $rows->map(function ($s, $i) {
            $nama = trim(
                ($s->pendaftaran->first_name ?? '') . ' ' .
                ($s->pendaftaran->last_name ?? '')
            );

            return [
                $i + 1,
                $s->kode_sertifikat,
                $s->nomor_sertifikat ?? '-',
                $nama ?: '-',
                $s->pendaftaran?->pelatihan?->nama_pelatihan ?? '-',
                $s->tgl_terbit ? Carbon::parse($s->tgl_terbit)->format('d/m/Y') : '-',
                $s->diterbitkan_oleh ?? '-',
                $s->file ? 'Ada' : 'Tidak Ada',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode Sertifikat', 'Nomor Sertifikat',
            'Nama Peserta', 'Pelatihan', 'Tgl Terbit',
            'Diterbitkan Oleh', 'File PDF',
        ];
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
                $lastCol = 'H';

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

                    // Warna kolom File PDF
                    $filVal   = $sheet->getCell("H{$row}")->getValue();
                    $filColor = $filVal === 'Ada' ? 'FFE8F5E9' : 'FFFFEBEE';
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => $filColor]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                $sheet->freezePane('A2');
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Total
                $totalRow = $lastRow + 2;
                $sheet->setCellValue("A{$totalRow}", "Total Sertifikat Terbit: {$this->rowCount}");
                $sheet->getStyle("A{$totalRow}")->applyFromArray([
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
        return 'Sertifikat';
    }
}