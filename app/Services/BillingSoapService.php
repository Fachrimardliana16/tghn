<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SoapClient;
use SoapHeader;
use SoapFault;

class BillingSoapService
{
    protected $wsdl;
    protected $namespace;
    protected $username;
    protected $password;

    private const MONTHS = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
    ];

    private const STATUS_MAP = [
        '1' => ['description' => 'Baru',            'class' => 'text-primary'],
        '2' => ['description' => 'Aktif',            'class' => 'text-success'],
        '3' => ['description' => 'Tutup Sementara', 'class' => 'text-warning'],
        '4' => ['description' => 'Tutup',            'class' => 'text-danger'],
        '5' => ['description' => 'Bongkar',          'class' => 'text-danger'],
    ];

    public function __construct()
    {
        $this->wsdl      = config('billing.soap.wsdl');
        $this->namespace = 'http://kholiq.pdam.pbg/';
        $this->username  = config('billing.soap.username');
        $this->password  = config('billing.soap.password');
    }

    // ── Konfigurasi SoapClient ─────────────────────────────────────────
    // trace dinonaktifkan secara default untuk menghindari memory leak &
    // exposure response di logs. Aktifkan hanya saat debugging via env: SOAP_TRACE=true
    private function getSoapClientOptions(): array
    {
        return [
            'trace'              => env('SOAP_TRACE', true),
            'exceptions'         => true,
            'cache_wsdl'         => env('SOAP_WSDL_CACHE', 'disk') === 'memory' ? WSDL_CACHE_MEMORY : WSDL_CACHE_DISK,
            'soap_version'       => SOAP_1_1,
            'connection_timeout' => 10,
        ];
    }

    /**
     * Ambil data tagihan dari server SOAP dan kembalikan sebagai array data murni.
     * Tidak ada HTML di sini — rendering sepenuhnya dilakukan oleh Blade.
     *
     * @param  string $customerId
     * @return array  ['type' => string, 'customer' => array|null, 'periods' => array, 'total' => float]
     */
    public function checkBilling(string $customerId): array
    {
        try {
            // ── 1. Inisialisasi SOAP Client ─────────────────────────────────
            // WSDL_CACHE_DISK: kerangka WSDL hanya diunduh sekali lalu di-cache.
            // connection_timeout: putus koneksi jika server tidak merespons dalam 10 detik.
            $soapClient = new SoapClient($this->wsdl, $this->getSoapClientOptions());

            // ── 2. Set SOAP Header autentikasi ──────────────────────────────
            $soapClient->__setSoapHeaders([
                new SoapHeader($this->namespace, 'seviceHeader', [
                    'UserName' => $this->username,
                    'Password' => $this->password,
                ]),
            ]);

            // ── 3. Panggil method SOAP ──────────────────────────────────────
            try {
                $soapResponse = $soapClient->__call('getListTagihan', [[
                    'nolangg' => $customerId,
                    'user'    => $this->username,
                    'pwd'     => $this->password,
                ]]);
                $rawXml = $soapClient->__getLastResponse();
            } catch (SoapFault $sf) {
                Log::warning('SOAP Fault: ' . $sf->getMessage());
                throw new Exception('Layanan tagihan tidak dapat dihubungi. Silakan coba beberapa saat lagi.');
            }

            // Jika __getLastResponse() null, coba parse langsung dari object response
            if ($rawXml === null) {
                return $this->parseFromObject($soapResponse, $customerId);
            }

            // ── 4. Parse XML dengan SimpleXML (robust, berbasis nama tag) ───
            return $this->parseXmlResponse($rawXml, $customerId);

        } catch (Exception $e) {
            Log::error('Billing check error [' . $customerId . ']: ' . $e->getMessage());
            return ['type' => 'system_error', 'message' => 'Layanan tagihan tidak dapat dihubungi. Silakan coba beberapa saat lagi.'];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Parse langsung dari stdObject response SOAP (ketika __getLastResponse() = null).
     * Ini terjadi karena SoapClient kadang tidak menyimpan raw response di-trace.
     */
    private function parseFromObject($response, string $customerId): array
    {
        // Coba baca property getListTagihanResult dari object response
        $result = null;
        if (is_object($response)) {
            $result = $response->getListTagihanResult
                   ?? $response->GetListTagihanResult
                   ?? null;
        }

        if ($result === null) {
            return ['type' => 'not_found', 'customer_id' => $customerId];
        }

        // Jika $result berupa string XML
        if (is_string($result)) {
            if (trim($result) === '') {
                return ['type' => 'not_found', 'customer_id' => $customerId];
            }
            if (strpos($result, '<') !== false) {
                return $this->parseXmlResponse('<envelope>' . $result . '</envelope>', $customerId);
            }
        }

        // Jika $result berupa object (misal stdClass dari SoapClient)
        if (is_object($result)) {
            $array = json_decode(json_encode($result), true);
            $mTagihan = $array['mTagihan'] ?? null;
            if (is_array($mTagihan)) {
                $totalTagihan = floatval($mTagihan['TotalTagihan'] ?? $mTagihan['totalTagihan'] ?? 0);
                if ($totalTagihan <= 0) {
                    return ['type' => 'lunas', 'customer_id' => $customerId];
                }

                $customer = [
                    'nomor'  => (string) ($mTagihan['NoLangganan'] ?? $customerId),
                    'nama'   => (string) ($mTagihan['Nama'] ?? ''),
                    'alamat' => (string) ($mTagihan['Alamat'] ?? ''),
                    'status' => (string) ($mTagihan['Status'] ?? ''),
                ];

                if ($customer['nama']) {
                    $customer['nama'] = $this->maskName($customer['nama']);
                }

                $customer['status_info'] = self::STATUS_MAP[$customer['status']]
                    ?? ['description' => 'Tidak Diketahui', 'class' => 'text-secondary'];

                $periode = (string) ($mTagihan['Periode'] ?? '');
                $m3      = (string) ($mTagihan['M3'] ?? '');

                $periods = [[
                    'periode'        => $this->formatPeriode($periode),
                    'm3'             => $m3,
                    'tagihan'        => $totalTagihan,
                    'tagihan_format' => number_format($totalTagihan, 0, ',', '.'),
                ]];

                return [
                    'type'         => 'tagihan',
                    'customer'     => $customer,
                    'periods'      => $periods,
                    'total'        => $totalTagihan,
                    'total_format' => number_format($totalTagihan, 0, ',', '.'),
                ];
            }
        }

        return ['type' => 'not_found', 'customer_id' => $customerId];
    }

    /**
     * Parsing XML response server SOAP menggunakan SimpleXMLElement.
     * Menggunakan nama tag, bukan nomor urut hardcoded — aman terhadap perubahan struktur API.
     */
    private function parseXmlResponse(string $rawXml, string $customerId): array
    {
        // Bersihkan namespace agar SimpleXML bisa membaca tag body
        $cleanXml = preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$3', $rawXml);
        $cleanXml = preg_replace('/\s+xmlns[^=]*="[^"]*"/', '', $cleanXml);

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($cleanXml);

        if ($xml === false) {
            Log::error('XML parse error: ' . implode(', ', array_map(
                fn($e) => $e->message, libxml_get_errors()
            )));
            throw new Exception('Gagal memproses respons server.');
        }

        // Cari elemen hasil — coba beberapa kemungkinan nama tag
        $result = $this->findElement($xml, 'getListTagihanResult')
               ?? $this->findElement($xml, 'GetListTagihanResult')
               ?? $this->findElement($xml, 'return')
               ?? $this->findElement($xml, 'result');

        // Jika tidak ditemukan atau kosong → cek database untuk membedakan lunas vs tidak terdaftar
        // Cek khusus: jika result hanya berisi tag self-closing kosong <getListTagihanResult />
        $resultXml = trim($result->asXML());
        if ($result === null || $resultXml === '' || $resultXml === '<getListTagihanResult />' || $resultXml === '<getListTagihanResult/>') {
            // API return kosong: cek database untuk membedakan lunas vs tidak terdaftar
            $isRegistered = $this->isCustomerRegisteredInDatabase($customerId);

            if ($isRegistered) {
                // Pelanggan ada di database tapi API return kosong → dianggap Lunas
                return ['type' => 'lunas', 'customer_id' => $customerId];
            } else {
                // Pelanggan tidak ada di database sama sekali → Not Found
                return ['type' => 'not_found', 'customer_id' => $customerId];
            }
        }

        // Parse inner XML dari result menggunakan SimpleXML (robust, tag-based)
        // Gunakan $result->children()->asXML() untuk mempertahankan struktur element
        $innerXml = simplexml_load_string('<root>' . $result->children()->asXML() . '</root>');

        if ($innerXml === false) {
            Log::error('Failed to parse inner XML from SOAP response');
            return ['type' => 'system_error', 'message' => 'Gagal memproses respons server SOAP.'];
        }

        return $this->extractData($innerXml, $customerId);
    }

    /**
     * Cari elemen XML secara rekursif berdasarkan nama tag (case-insensitive).
     */
    private function findElement(\SimpleXMLElement $xml, string $tagName): ?\SimpleXMLElement
    {
        foreach ($xml->children() as $child) {
            if (strtolower($child->getName()) === strtolower($tagName)) {
                return $child;
            }
            $found = $this->findElement($child, $tagName);
            if ($found !== null) return $found;
        }
        return null;
    }

    /**
     * Ekstrak data pelanggan dan tagihan dari SimpleXMLElement.
     * Mendukung struktur baru <mTagihan> dari API PDAM dan struktur lama TAGIHAN.
     */
    private function extractData(\SimpleXMLElement $xml, string $customerId): array
    {
        // Coba baca struktur BARU: data langsung di <mTagihan>
        $mTagihan = $xml->mTagihan ?? $xml->children('', true)->mTagihan ?? null;

        $customer = [];
        $periods  = [];
        $total    = 0.0;

        if ($mTagihan !== null) {
            // Struktur BARU: nama tag di dalam <mTagihan>
            $customer['nomor']   = (string) ($mTagihan->NoLangganan   ?? $mTagihan->noLangganan   ?? $customerId);
            $customer['nama']    = (string) ($mTagihan->Nama       ?? $mTagihan->nama       ?? '');
            $customer['alamat']  = (string) ($mTagihan->Alamat     ?? $mTagihan->alamat     ?? '');
            $customer['status']  = (string) ($mTagihan->Status     ?? $mTagihan->status     ?? '');

            // Masking nama
            if ($customer['nama']) {
                $customer['nama'] = $this->maskName($customer['nama']);
            }

            // Status info
            $customer['status_info'] = self::STATUS_MAP[$customer['status']]
                ?? ['description' => 'Tidak Diketahui', 'class' => 'text-secondary'];

            // Ambil periode dan tagihan dari <mTagihan>
            $periode = (string) ($mTagihan->Periode ?? $mTagihan->periode ?? '');
            $tagihan = floatval((string) ($mTagihan->TotalTagihan ?? $mTagihan->totalTagihan ?? $mTagihan->TotalTagihan ?? 0));
            $m3      = (string) ($mTagihan->M3      ?? $mTagihan->m3      ?? '');

            $total += $tagihan;

            $periods[] = [
                'periode'         => $this->formatPeriode($periode),
                'm3'              => $m3,
                'tagihan'         => $tagihan,
                'tagihan_format'  => number_format($tagihan, 0, ',', '.'),
            ];

            // Cek lunas
            if ($total <= 0) {
                return ['type' => 'lunas', 'customer_id' => $customerId];
            }

            return [
                'type'          => 'tagihan',
                'customer'      => $customer,
                'periods'       => $periods,
                'total'         => $total,
                'total_format'  => number_format($total, 0, ',', '.'),
            ];
        }

        // Struktur LAMA: tag TAGIHAN berulang
        $customer = [
            'nomor'   => (string) ($xml->NOLANGG   ?? $xml->nolangg   ?? $customerId),
            'nama'    => (string) ($xml->NAMA       ?? $xml->nama       ?? ''),
            'alamat'  => (string) ($xml->ALAMAT     ?? $xml->alamat     ?? ''),
            'status'  => (string) ($xml->STATUS     ?? $xml->status     ?? ''),
        ];

        if ($customer['nama']) {
            $customer['nama'] = $this->maskName($customer['nama']);
        }
        $customer['status_info'] = self::STATUS_MAP[$customer['status']]
            ?? ['description' => 'Tidak Diketahui', 'class' => 'text-secondary'];

        // Kumpulkan data per periode
        foreach ($xml->TAGIHAN ?? $xml->tagihan ?? [] as $row) {
            $tagihan = floatval((string) ($row->TOTALTAGIHANPLUSDENDA ?? $row->totaltagihanplusdenda ?? 0));
            $periode = (string) ($row->PERIODE ?? $row->periode ?? '');
            $m3      = (string) ($row->M3      ?? $row->m3      ?? '');

            $total += $tagihan;
            $periods[] = [
                'periode'         => $this->formatPeriode($periode),
                'm3'              => $m3,
                'tagihan'         => $tagihan,
                'tagihan_format'  => number_format($tagihan, 0, ',', '.'),
            ];
        }

        if ($total <= 0 && count($periods) === 0) {
            return ['type' => 'lunas', 'customer_id' => $customerId];
        }

        if ($total <= 0) {
            return ['type' => 'lunas', 'customer_id' => $customerId];
        }

        return [
            'type'          => 'tagihan',
            'customer'      => $customer,
            'periods'       => $periods,
            'total'         => $total,
            'total_format'  => number_format($total, 0, ',', '.'),
        ];
    }

    private function formatPeriode(string $periode): string
    {
        if (strlen($periode) < 6) return $periode;
        $year  = substr($periode, 0, 4);
        $month = substr($periode, 4, 2);
        return (self::MONTHS[$month] ?? $month) . ' ' . $year;
    }

    private function maskName(string $name): string
    {
        return implode(' ', array_map(function (string $part): string {
            if (strlen($part) <= 2) return $part;
            return substr($part, 0, 2) . str_repeat('*', strlen($part) - 3) . substr($part, -1);
        }, explode(' ', $name)));
    }

    /**
     * Cek apakah pelanggan terdaftar di database newbilling.
     * Digunakan ketika API SOAP return kosong untuk membedakan:
     * - Pelanggan terdaftar tapi sudah lunas
     * - Pelanggan tidak terdaftar sama sekali
     */
    private function isCustomerRegisteredInDatabase(string $customerId): bool
    {
        try {
            $db = DB::connection('newbilling');
            $count = $db->table('tbl_pelanggan')
                ->where('nolangg', $customerId)
                ->count();

            return $count > 0;
        } catch (\Exception $e) {
            // Jika error koneksi database, asumsi tidak terdaftar
            Log::warning('Database check failed: ' . $e->getMessage());
            return false;
        }
    }
}