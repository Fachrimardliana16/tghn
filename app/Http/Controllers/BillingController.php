<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BillingSoapService;
use Illuminate\Support\Facades\Cache;

class BillingController extends Controller
{
    protected BillingSoapService $billingService;

    public function __construct(BillingSoapService $billingService)
    {
        $this->billingService = $billingService;
    }

    public function index()
    {
        return view('billing.index');
    }

    public function check(Request $request)
    {
        // Validasi: wajib 8 digit angka
        if (!$request->has('nolangg') || !preg_match('/^\d{8}$/', $request->input('nolangg'))) {
            return response()->json([
                'type'    => 'validation_error',
                'status'  => 'error',
                'message' => 'Nomor Pelanggan harus berupa 8 digit angka.',
            ]);
        }

        $customerId = $request->input('nolangg');
        $cacheKey   = 'billing_' . $customerId;

        // 1. Ambil data dari cache jika ada
        $data = Cache::get($cacheKey);

        // 2. Jika tidak ada di cache (cache miss), panggil SOAP service
        if ($data === null) {
            $data = $this->billingService->checkBilling($customerId);

            // Simpan ke cache 5 menit hanya jika bukan system_error
            if (is_array($data) && ($data['type'] ?? '') !== 'system_error') {
                Cache::put($cacheKey, $data, 300);
            }
        }

        // 3. Tangani error sistem jika terjadi
        if (!is_array($data) || ($data['type'] ?? '') === 'system_error') {
            Cache::forget($cacheKey);
            return response()->json([
                'type'    => 'system_error',
                'status'  => 'error',
                'message' => $data['message'] ?? 'Layanan tagihan tidak dapat dihubungi. Silakan coba beberapa saat lagi.',
            ]);
        }

        // 4. Format response untuk frontend Blade template
        $response = [
            'status' => 'success',
        ];

        if ($data['type'] === 'lunas') {
            $waktu = $this->formatWaktuIndonesia();
            $response['type'] = 'lunas';
            $response['message'] = '<div class="alert alert-success">'
                . '<i class="fas fa-check-circle"></i> Nomor pelanggan <strong>' . htmlspecialchars($data['customer_id'] ?? $customerId) . '</strong> sudah lunas. Tidak ada tagihan yang harus dibayar.'
                . '<div style="margin-top:8px; padding-top:8px; border-top:1px solid rgba(0,0,0,0.1); font-size:0.85rem; opacity:0.85;">'
                . '<i class="fas fa-clock"></i> Diperiksa pada: <strong>' . $waktu . '</strong></div>'
                . '</div>';
        } elseif ($data['type'] === 'not_found') {
            $response['type'] = 'not_found';
            $waktu = $this->formatWaktuIndonesia();
            $response['message'] = '<div class="alert alert-danger">'
                . '<i class="fas fa-times-circle"></i> Nomor pelanggan tidak ditemukan atau sudah lunas. Jika yakin nomor benar, silakan hubungi kami.'
                . '<div style="margin-top:8px; padding-top:8px; border-top:1px solid rgba(0,0,0,0.1); font-size:0.85rem; opacity:0.85;">'
                . '<i class="fas fa-clock"></i> Diperiksa pada: <strong>' . $waktu . '</strong></div>'
                . '</div>';
        } elseif ($data['type'] === 'tagihan') {
            $response['type'] = 'tagihan';
            $response['message'] = $this->renderBillingTable($data);
        } else {
            $response['type'] = 'system_error';
            $response['status'] = 'error';
            $response['message'] = $data['message'] ?? 'Layanan tagihan tidak dapat dihubungi.';
        }

        return response()->json($response);
    }

    /**
     * Format waktu dalam Bahasa Indonesia.
     */
    private function formatWaktuIndonesia(): string
    {
        $hari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $bulan = [
            'January' => 'Januari', 'February' => 'Februari', 'Maret' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
        ];

        $now = now();
        $namaHari = $hari[$now->format('l')] ?? $now->format('l');
        $namaBulan = $bulan[$now->format('F')] ?? $now->format('F');

        return $namaHari . ', ' . $now->format('d') . ' ' . $namaBulan . ' ' . $now->format('Y H:i:s');
    }

    /**
     * Render tabel tagihan ke HTML untuk frontend Blade template.
     */
    private function renderBillingTable(array $data): string
    {
        $html = '<div class="alert alert-info"><h4><i class="fas fa-receipt"></i> Detail Tagihan</h4>';

        // Customer info
        $c = $data['customer'] ?? [];
        $html .= '<p><strong>Nomor Pelanggan:</strong> ' . ($c['nomor'] ?? '-') . '</p>';
        $html .= '<p><strong>Nama:</strong> ' . ($c['nama'] ?? '-') . '</p>';
        $html .= '<p><strong>Alamat:</strong> ' . ($c['alamat'] ?? '-') . '</p>';
        $html .= '<p><strong>Status:</strong> <span class="' . ($c['status_info']['class'] ?? 'text-secondary') . '">' . ($c['status_info']['description'] ?? '-') . '</span></p>';

        // Periods table
        $html .= '<table class="table mt-3">';
        $html .= '<thead><tr><th>Periode</th><th>M3</th><th>Tagihan</th></tr></thead><tbody>';

        foreach ($data['periods'] ?? [] as $p) {
            $html .= '<tr>';
            $html .= '<td>' . ($p['periode'] ?? '-') . '</td>';
            $html .= '<td>' . ($p['m3'] ?? '-') . '</td>';
            $html .= '<td><strong>' . ($p['tagihan_format'] ?? '0') . '</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '<tfoot><tr><td colspan="3">Total: <strong>' . ($data['total_format'] ?? '0') . '</strong></td></tr></tfoot>';
        $html .= '</table>';

        // Waktu pengecekan — terpisah dari data pelanggan
        $waktu = $this->formatWaktuIndonesia();
        $html .= '<div style="margin-top:16px; padding-top:12px; border-top:1px solid var(--border); font-size:0.85rem; color:var(--text-muted);">';
        $html .= '<i class="fas fa-clock"></i> Diperiksa pada: <strong>' . $waktu . '</strong>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}
