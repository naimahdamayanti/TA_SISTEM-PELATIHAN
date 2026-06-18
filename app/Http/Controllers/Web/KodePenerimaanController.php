<?php

namespace App\Http\Controllers\Web;

use App\Helpers\MailHelper;                   
use App\Http\Controllers\Controller;
use App\Models\KodePenerimaanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KodePenerimaanController extends Controller
{
    public function index()
    {
        $kodes = KodePenerimaanModel::with('pemakainya')
                                    ->latest()
                                    ->paginate(15);

        return view('admin.kode-penerimaan.index', compact('kodes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_peruntukan' => 'nullable|string|max:255',
            'email_tujuan'    => 'nullable|email',
            'nama_tujuan'     => 'nullable|string|max:255',
            'expired_at'      => 'nullable|date|after:today',
            'jumlah'          => 'required|integer|min:1|max:20',
        ]);

        // Kirim email hanya bisa untuk 1 kode sekaligus
        if ($request->filled('email_tujuan') && $request->jumlah > 1) {
            return back()->withErrors([
                'email_tujuan' => 'Pengiriman email hanya bisa dilakukan untuk 1 kode sekaligus.',
            ])->withInput();
        }

        $kodesDibuat = [];

        for ($i = 0; $i < $request->jumlah; $i++) {
            $kode = KodePenerimaanModel::create([
                'kode'            => KodePenerimaanModel::generateKode(),
                'nama_peruntukan' => $request->nama_peruntukan,
                'expired_at'      => $request->expired_at,
                'created_by'      => auth()->id(),
            ]);

            $kodesDibuat[] = $kode;
        }

        if ($request->filled('email_tujuan')) {
            $kode      = $kodesDibuat[0];
            $nama      = $request->nama_tujuan ?: $request->nama_peruntukan ?: 'Instruktur';
            $expiredAt = $kode->expired_at?->format('d/m/Y');

            $result = MailHelper::sendKodePenerimaanEmail(
                $request->email_tujuan,
                $nama,
                $kode->kode,
                $expiredAt,
            );

            if ($result['status'] === 'success') {
                return redirect()->route('admin.kode-penerimaan.index')
                                 ->with('success', "Kode berhasil dibuat dan dikirim ke {$request->email_tujuan}.");
            }

            return redirect()->route('admin.kode-penerimaan.index')
                             ->with('warning', 'Kode berhasil dibuat, tapi email gagal dikirim. Kirim ulang secara manual.');
        }

        return redirect()->route('admin.kode-penerimaan.index')
                         ->with('success', count($kodesDibuat) . ' kode berhasil dibuat.');
    }

    public function kirimEmail(Request $request, KodePenerimaanModel $kodePenerimaan)
    {
        $request->validate([
            'email_tujuan' => 'required|email',
            'nama_tujuan'  => 'nullable|string|max:255',
        ]);

        if ($kodePenerimaan->is_used) {
            return back()->with('error', 'Kode ini sudah digunakan, tidak perlu dikirim ulang.');
        }

        $nama      = $request->nama_tujuan ?: $kodePenerimaan->nama_peruntukan ?: 'Instruktur';
        $expiredAt = $kodePenerimaan->expired_at?->format('d/m/Y');

        $result = MailHelper::sendKodePenerimaanEmail(
            $request->email_tujuan,
            $nama,
            $kodePenerimaan->kode,
            $expiredAt,
        );

        if ($result['status'] === 'success') {
            return back()->with('success', "Kode berhasil dikirim ke {$request->email_tujuan}.");
        }

        return back()->with('error', 'Email gagal dikirim. Periksa konfigurasi SMTP.');
    }

    public function destroy(KodePenerimaanModel $kodePenerimaan)
    {
        if ($kodePenerimaan->is_used) {
            return back()->with('error', 'Kode yang sudah digunakan tidak dapat dihapus.');
        }

        $kodePenerimaan->delete();

        return back()->with('success', 'Kode berhasil dihapus.');
    }
}