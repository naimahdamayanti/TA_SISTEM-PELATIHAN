<?php

namespace App\Exports;

use App\Exports\Sheets\PelatihanSheet;
use App\Exports\Sheets\PendaftaranSheet;
use App\Exports\Sheets\SertifikatSheet;
use App\Exports\Sheets\KehadiranSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExcelExport implements WithMultipleSheets
{
    public function __construct(private int $tahun) {}

    public function sheets(): array
    {
        return [
            new PelatihanSheet($this->tahun),
            new PendaftaranSheet($this->tahun),
            new SertifikatSheet($this->tahun),
            new KehadiranSheet($this->tahun),
        ];
    }
}