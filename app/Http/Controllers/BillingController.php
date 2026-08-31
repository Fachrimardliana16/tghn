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
        $c = $data['customer'] ?? [];
        $waktu = $this->formatWaktuIndonesia();

        $html = '<div class="billing-result-card">';
        
        // Header Title
        $html .= '<div class="billing-title"><i class="fas fa-file-invoice-dollar"></i> Detail Tagihan Pelanggan</div>';

        // Customer Info Grid
        $html .= '<div class="customer-info-grid">';
        $html .= '<div class="info-item"><span class="info-label">Nomor Pelanggan</span><span class="info-value">' . htmlspecialchars($c['nomor'] ?? '-') . '</span></div>';
        $html .= '<div class="info-item"><span class="info-label">Nama Pelanggan</span><span class="info-value">' . htmlspecialchars($c['nama'] ?? '-') . '</span></div>';
        $html .= '<div class="info-item"><span class="info-label">Alamat</span><span class="info-value">' . htmlspecialchars($c['alamat'] ?? '-') . '</span></div>';
        $html .= '<div class="info-item"><span class="info-label">Status</span><span class="info-value ' . ($c['status_info']['class'] ?? 'text-secondary') . '"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($c['status_info']['description'] ?? '-') . '</span></div>';
        $html .= '</div>';

        // Periods Table
        $html .= '<div class="billing-table-wrapper">';
        $html .= '<table class="billing-table">';
        $html .= '<thead><tr><th>Periode</th><th style="text-align:center;">M³</th><th style="text-align:right;">Tagihan</th></tr></thead><tbody>';

        foreach ($data['periods'] ?? [] as $p) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($p['periode'] ?? '-') . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars($p['m3'] ?? '-') . '</td>';
            $html .= '<td style="text-align:right;" class="tagihan-amount">Rp. ' . ($p['tagihan_format'] ?? '0') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '<tfoot><tr class="total-row"><td colspan="2" class="total-label">Total Tagihan</td><td style="text-align:right;" class="total-amount">Rp. ' . ($data['total_format'] ?? '0') . '</td></tr></tfoot>';
        $html .= '</table>';
        $html .= '</div>';

        // Informasi Loket / Kanal Pembayaran Resmi
        $html .= '<div class="payment-info-box">';
        $html .= '<div class="payment-info-header"><i class="fas fa-store-alt"></i> Tempat & Loket Pembayaran Resmi</div>';
        $html .= '<div class="payment-group"><span class="pay-cat">Bank:</span><span class="pay-list">BNI, BRI, BTN, BSI, Bank Jateng, BPRS BMP, BPR BKK, KB Bank</span></div>';
        $html .= '<div class="payment-group"><span class="pay-cat">E-Wallet:</span><span class="pay-list">GoPay, OVO, DANA, LinkAja, ShopeePay, Flip, KIPO</span></div>';
        $html .= '<div class="payment-group"><span class="pay-cat">Marketplace:</span><span class="pay-list">Shopee, Tokopedia, Bukalapak, Blibli</span></div>';
        $html .= '<div class="payment-group"><span class="pay-cat">Gerai & Pos:</span><span class="pay-list">Alfamart, Indomaret, & PT Pos Indonesia</span></div>';
        $html .= '</div>';

        // Timestamp Footer
        $html .= '<div class="timestamp-footer"><i class="fas fa-clock"></i> Diperiksa pada: <strong>' . $waktu . '</strong></div>';

        $html .= '</div>';

        return $html;
    }
}
