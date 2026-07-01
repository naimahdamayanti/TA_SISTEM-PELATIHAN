<?php

namespace App\Exports\Sheets;

use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\KualifikasiSertifikasiModel;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PelatihanSheet implements
    FromCollection, WithHeadings, WithStyles, WithTitle,
    ShouldAutoSize, WithEvents
{
    private int $rowCount = 0;

    public function __construct(private int $tahun) {}

    public function collection()
    {
        $rows = PelatihanModel::with('instruktur')
            ->whereYear('tgl_mulai', $this->tahun)
            ->orderBy('tgl_mulai')
            ->get();

        $pelIds = $rows->pluck('id_pelatihan');

        $countDiterima = PendaftaranModel::whereIn('pelatihan_id', $pelIds)
            ->where('status', 'diterima')
            ->selectRaw('pelatihan_id, COUNT(*) as total')
            ->groupBy('pelatihan_id')
            ->pluck('total', 'pelatihan_id');

        $countMenunggu = PendaftaranModel::whereIn('pelatihan_id', $pelIds)
            ->where('status', 'menunggu')
            ->selectRaw('pelatihan_id, COUNT(*) as total')
            ->groupBy('pelatihan_id')
            ->pluck('total', 'pelatihan_id');

        $countDitolak = PendaftaranModel::whereIn('pelatihan_id', $pelIds)
            ->where('status', 'ditolak')
            ->selectRaw('pelatihan_id, COUNT(*) as total')
            ->groupBy('pelatihan_id')
            ->pluck('total', 'pelatihan_id');

        $countSertif = SertifikatModel::whereHas('pendaftaran', fn($q) =>
            $q->whereIn('pelatihan_id', $pelIds)
        )
        ->join('pendaftaran', 'sertifikat.pendaftaran_id', '=', 'pendaftaran.id_pendaftaran')
        ->selectRaw('pendaftaran.pelatihan_id, COUNT(*) as total')
        ->groupBy('pendaftaran.pelatihan_id')
        ->pluck('total', 'pendaftaran.pelatihan_id');

        $countLulus = KualifikasiSertifikasiModel::whereHas('pendaftaran', fn($q) =>
                $q->whereIn('pelatihan_id', $pelIds)
            )
            ->where('memenuhi_syarat', true)
            ->with('pendaftaran:id_pendaftaran,pelatihan_id')
            ->get()
            ->groupBy(fn($k) => $k->pendaftaran->pelatihan_id)
            ->map->count();

        $this->rowCount = $rows->count();

        return $rows->map(function ($p, $i) use ($countDiterima, $countMenunggu, $countDitolak, $countSertif, $countLulus) {
            $diterima = (int) ($countDiterima[$p->id_pelatihan] ?? 0);
            $lulus    = (int) ($countLulus[$p->id_pelatihan] ?? 0);
            $persenKelulusan = $diterima > 0 ? round(($lulus / $diterima) * 100, 1) : '-';

            return [
                $i + 1,
                $p->kode_pelatihan,
                $p->nama_pelatihan,
                $p->kategori ?? '-',
                $p->instruktur?->nama ?? '-',
                (int) $p->kuota,
                $diterima,
                (int) ($countMenunggu[$p->id_pelatihan] ?? 0),
                (int) ($countDitolak[$p->id_pelatihan] ?? 0),
                (int) ($countSertif[$p->id_pelatihan] ?? 0),
                $persenKelulusan,
                $p->tgl_mulai   ? Carbon::parse($p->tgl_mulai)->format('d/m/Y')   : '-',
                $p->tgl_selesai ? Carbon::parse($p->tgl_selesai)->format('d/m/Y') : '-',
                ucfirst($p->status),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Kode', 'Nama Pelatihan', 'Kategori', 'Instruktur',
            'Kuota', 'Diterima', 'Menunggu', 'Ditolak', 'Sertifikat', 'Kelulusan (%)',
            'Tgl Mulai', 'Tgl Selesai', 'Status',
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
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 1;

                $sheet->getStyle("A1:N{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => 'FFD0D0D0'],
                        ],
                    ],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 === 0) {
                        $sheet->getStyle("A{$row}:N{$row}")->applyFromArray([
                            'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFF0F4FF']],
                        ]);
                    }
                }

                $sheet->freezePane('A2');

                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->getStyle("F2:K{$lastRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle("K2:K{$lastRow}")
                    ->getNumberFormat()->setFormatCode('0.0"%"');

                $totalRow = $lastRow + 1;
                $sheet->setCellValue("E{$totalRow}", 'TOTAL');
                $sheet->setCellValue("F{$totalRow}", "=SUM(F2:F{$lastRow})");
                $sheet->setCellValue("G{$totalRow}", "=SUM(G2:G{$lastRow})");
                $sheet->setCellValue("H{$totalRow}", "=SUM(H2:H{$lastRow})");
                $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$lastRow})");
                $sheet->setCellValue("J{$totalRow}", "=SUM(J2:J{$lastRow})");
                $sheet->setCellValue("K{$totalRow}", "=AVERAGE(K2:K{$lastRow})");

                $sheet->getStyle("K{$totalRow}")
                    ->getNumberFormat()->setFormatCode('0.0"%"');

                $sheet->getStyle("E{$totalRow}:K{$totalRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1565C0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $infoRow = $totalRow + 2;
                $sheet->setCellValue("A{$infoRow}", 'Dicetak pada: ' . now()->translatedFormat('j F Y, H:i'));
                $sheet->getStyle("A{$infoRow}")->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['argb' => 'FF888888'], 'size' => 9],
                ]);
            },
        ];
    }

    public function title(): string
    {
        return 'Pelatihan';
    }
}