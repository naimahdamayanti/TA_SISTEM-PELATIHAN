<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KualifikasiSertifikasiModel ;
use App\Models\PelatihanModel;
use App\Models\PendaftaranModel;
use App\Models\SertifikatModel;
use App\Models\UserModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SertifikatController extends Controller
{
    /* ═══════════════════════════════════════════════
     |  GET /api/sertifikat/cek?kode=CERT-XXX-YYY
     |  Public — tanpa auth token
     ═══════════════════════════════════════════════ */

    public function cek(Request $request): JsonResponse
    {
        $request->validate([
            'kode' => 'required|string|max:20',
        ]);

        $sertifikat = SertifikatModel::with([
            'pendaftaran.peserta:id_user,nama',
            'pendaftaran.pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan,tgl_mulai,tgl_selesai',
        ])
            ->where('kode_sertifikat', strtoupper(trim($request->kode)))
            ->first();

        if (!$sertifikat) {
            return response()->json([
                'success' => false,
                'message' => 'Kode sertifikat tidak ditemukan.',
                'valid'   => false,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'valid'   => true,
            'data'    => [
                'kode_sertifikat'  => $sertifikat->kode_sertifikat,
                'nama_peserta'     => $sertifikat->pendaftaran->peserta?->nama
                    ?? trim($sertifikat->pendaftaran->first_name . ' ' . $sertifikat->pendaftaran->last_name),
                'nama_pelatihan'   => $sertifikat->pendaftaran->pelatihan->nama_pelatihan,
                'kode_pelatihan'   => $sertifikat->pendaftaran->pelatihan->kode_pelatihan,
                'periode'          => [
                    'mulai'   => $sertifikat->pendaftaran->pelatihan->tgl_mulai,
                    'selesai' => $sertifikat->pendaftaran->pelatihan->tgl_selesai,
                ],
                'tgl_terbit'       => $sertifikat->tgl_terbit,
                'diterbitkan_oleh' => $sertifikat->diterbitkan_oleh,
            ],
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/sertifikat
     |  Admin      → semua sertifikat
     |  Instruktur → sertifikat di pelatihannya
     |  Peserta    → sertifikat miliknya
     ═══════════════════════════════════════════════ */

    public function index(Request $request): JsonResponse
    {
        /** @var UserModel $user */
        $user = $request->user();

        $query = SertifikatModel::with([
            'pendaftaran.peserta:id_user,nama,email',
            'pendaftaran.pelatihan:id_pelatihan,nama_pelatihan,kode_pelatihan',
        ])->latest('tgl_terbit');

        match ($user->role) {
            'instruktur' => $query->whereHas('pendaftaran.pelatihan', fn($q) =>
                $q->where('instruktur_id', $user->id_user)
            ),
            'peserta' => $query->whereHas('pendaftaran', fn($q) =>
                $q->where('peserta_id', $user->id_user)
            ),
            default => null,
        };

        if ($request->filled('search') && $user->role === 'admin') {
            $query->where('kode_sertifikat', 'like', '%' . $request->search . '%')
                ->orWhereHas('pendaftaran', fn($q) =>
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                );
        }
        if ($request->filled('pelatihan_id')) {
            $query->whereHas('pendaftaran', fn($q) =>
                $q->where('pelatihan_id', $request->pelatihan_id)
            );
        }

        // Tambahkan URL download ke setiap record
        $data = $query->paginate($request->input('per_page', 15));
        $data->getCollection()->transform(fn($s) => array_merge($s->toArray(), [
            'download_url' => route('api.sertifikat.download', $s->id_sertifikat),
        ]));

        return response()->json([
            'success' => true,
            'data'    => $data,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/sertifikat/generate/{pendaftaran_id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function generate(Request $request, int $pendaftaranId): JsonResponse
    {
        $this->onlyAdmin($request);

        $pendaftaran = PendaftaranModel::with([
            'peserta',
            'pelatihan.instruktur',
            'kualifikasiSertifikasi',
        ])->findOrFail($pendaftaranId);

        if (!$pendaftaran->kualifikasiSertifikasi?->memenuhi_syarat) {
            return response()->json([
                'success' => false,
                'message' => 'Peserta belum memenuhi syarat sertifikasi.',
            ], 422);
        }

        if (SertifikatModel::where('pendaftaran_id', $pendaftaranId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Sertifikat untuk peserta ini sudah pernah diterbitkan.',
            ], 422);
        }

        $request->validate([
            'diterbitkan_oleh' => 'required|string|max:100',
        ]);

        $kode = $this->buatKodeUnik($pendaftaran->pelatihan->kode_pelatihan);
        $tglTerbit = Carbon::now();
        $filePath  = 'sertifikat/' . $kode . '.pdf';

        // Generate & simpan PDF
        $pdf = Pdf::loadView('sertifikat.template', [
            'pendaftaran'     => $pendaftaran,
            'peserta'         => $pendaftaran->peserta,
            'pelatihan'       => $pendaftaran->pelatihan,
            'instruktur'      => $pendaftaran->pelatihan->instruktur,
            'kode'            => $kode,
            'tgl_terbit'      => $tglTerbit,
            'diterbitkan_oleh'=> $request->diterbitkan_oleh,
            'persen_hadir'    => $pendaftaran->kualifikasiSertifikasi->persen_hadir,
        ])->setPaper('a4', 'landscape')->setOption('dpi', 150);

        Storage::disk('public')->put($filePath, $pdf->output());

        $sertifikat = SertifikatModel::create([
            'pendaftaran_id'  => $pendaftaranId,
            'kode_sertifikat' => $kode,
            'tgl_terbit'      => $tglTerbit,
            'diterbitkan_oleh'=> $request->diterbitkan_oleh,
            'file'            => $filePath,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => "Sertifikat {$kode} berhasil diterbitkan.",
            'data'         => $sertifikat,
            'download_url' => route('api.sertifikat.download', $sertifikat->id_sertifikat),
        ], 201);
    }

    /* ═══════════════════════════════════════════════
     |  POST /api/sertifikat/generate-massal/{pelatihan_id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function generateMassal(Request $request, int $pelatihanId): JsonResponse
    {
        $this->onlyAdmin($request);

        $request->validate([
            'diterbitkan_oleh' => 'required|string|max:100',
        ]);

        $pelatihan = PelatihanModel::with('instruktur')->findOrFail($pelatihanId);

        $kandidat = KualifikasiSertifikasiModel::where('memenuhi_syarat', true)
            ->whereHas('pendaftaran', fn($q) => $q->where('pelatihan_id', $pelatihanId))
            ->whereDoesntHave('pendaftaran.sertifikat')
            ->with(['pendaftaran.peserta'])
            ->get();

        if ($kandidat->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada peserta yang memenuhi syarat atau semua sudah memiliki sertifikat.',
            ], 422);
        }

        $tglTerbit = Carbon::now();
        $generated = [];

        foreach ($kandidat as $kual) {
            $kode     = $this->buatKodeUnik($pelatihan->kode_pelatihan);
            $filePath = 'sertifikat/' . $kode . '.pdf';

            $pdf = Pdf::loadView('sertifikat.template', [
                'pendaftaran'     => $kual->pendaftaran,
                'peserta'         => $kual->pendaftaran->peserta,
                'pelatihan'       => $pelatihan,
                'instruktur'      => $pelatihan->instruktur,
                'kode'            => $kode,
                'tgl_terbit'      => $tglTerbit,
                'diterbitkan_oleh'=> $request->diterbitkan_oleh,
                'persen_hadir'    => $kual->persen_hadir,
            ])->setPaper('a4', 'landscape')->setOption('dpi', 150);

            Storage::disk('public')->put($filePath, $pdf->output());

            $sertifikat = SertifikatModel::create([
                'pendaftaran_id'  => $kual->pendaftaran_id,
                'kode_sertifikat' => $kode,
                'tgl_terbit'      => $tglTerbit,
                'diterbitkan_oleh'=> $request->diterbitkan_oleh,
                'file'            => $filePath,
            ]);

            $generated[] = [
                'kode'     => $kode,
                'peserta'  => $kual->pendaftaran->peserta?->nama,
                'download' => route('api.sertifikat.download', $sertifikat->id_sertifikat),
            ];
        }

        return response()->json([
            'success'  => true,
            'message'  => count($generated) . ' sertifikat berhasil diterbitkan.',
            'data'     => $generated,
        ]);
    }

    /* ═══════════════════════════════════════════════
     |  GET /api/sertifikat/{id}/download
     |  Peserta → hanya miliknya
     |  Admin & Instruktur → semua
     ═══════════════════════════════════════════════ */

    public function download(Request $request, int $id)
    {
        /** @var UserModel $user */
        $user       = $request->user();
        $sertifikat = SertifikatModel::with('pendaftaran')->findOrFail($id);

        if ($user->role === 'peserta' && $sertifikat->pendaftaran->peserta_id !== $user->id_user) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($sertifikat->file)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        return $disk->download(
            $sertifikat->file,
            $sertifikat->kode_sertifikat . '.pdf'
        );
    }

    /* ═══════════════════════════════════════════════
     |  DELETE /api/sertifikat/{id}   [ADMIN]
     ═══════════════════════════════════════════════ */

    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->onlyAdmin($request);

        $sertifikat = SertifikatModel::findOrFail($id);

        if (Storage::disk('public')->exists($sertifikat->file)) {
            Storage::disk('public')->delete($sertifikat->file);
        }

        $sertifikat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sertifikat berhasil dihapus.',
        ]);
    }

    /* ─── Helper ─── */

    private function buatKodeUnik(string $kodepelatihan): string
    {
        do {
            $kode = 'CERT-' . strtoupper($kodepelatihan) . '-' . strtoupper(Str::random(6));
        } while (SertifikatModel::where('kode_sertifikat', $kode)->exists());

        return $kode;
    }

    private function onlyAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') {
            abort(response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403));
        }
    }
}
