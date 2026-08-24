<?php

namespace App\Services;

use Exception;
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

    public function __construct()
    {
        $this->wsdl = config('billing.soap.wsdl');
        $this->namespace = 'http://kholiq.pdam.pbg/';
        $this->username = config('billing.soap.username');
        $this->password = config('billing.soap.password');
    }

    public function checkBilling($customerId)
    {
        try {
            // Inisialisasi SOAP Client
            $soapClient = new SoapClient(
                $this->wsdl,
                [
                    'trace' => true,
                    'exceptions' => true,
                    'cache_wsdl' => WSDL_CACHE_NONE,
                    'soap_version' => SOAP_1_1
                ]
            );

            // Set SOAP Headers
            $headers = new SoapHeader(
                $this->namespace,
                'seviceHeader', // Typo dari sistem lama
                [
                    'UserName' => $this->username,
                    'Password' => $this->password
                ]
            );
            $soapClient->__setSoapHeaders([$headers]);

            // Persiapkan parameter
            $params = [
                'nolangg' => $customerId,
                'user' => $this->username,
                'pwd' => $this->password
            ];

            // Panggil service
            try {
                $info = $soapClient->__call("getListTagihan", array($params));
                $simple = $soapClient->__getLastResponse();
            } catch (SoapFault $sf) {
                throw new Exception('Gagal terhubung ke server. Silakan coba beberapa saat lagi.');
            }

            // Parse XML
            $p = xml_parser_create();
            if (!xml_parse_into_struct($p, $simple, $vals, $index)) {
                throw new Exception('Gagal memproses data');
            }
            xml_parser_free($p);

            // Cek keberadaan data
            if (!isset($vals) || empty($vals)) {
                return [
                    'status' => 'error',
                    'message' => "<div class='alert alert-warning' style='background: #fff3cd; color: #856404; border-color: #ffeeba;'><i class='fas fa-exclamation-triangle'></i> Nomor pelanggan tidak ditemukan. Silakan hubungi <a href='https://pengaduan.pdampurbalingga.co.id' style='font-weight: bold; color: #533f03;'>layanan pengaduan</a> untuk memeriksa nomor langganan Anda.</div>"
                ];
            }

            // Inisialisasi nilai default
            $total = 0;
            $per = "";

            // Cek dan proses data tagihan
            if (isset($index['TOTALTAGIHANPLUSDENDA']) && !empty($index['TOTALTAGIHANPLUSDENDA'])) {
                $dt = $index['TOTALTAGIHANPLUSDENDA'];
                foreach ($dt as $key => $value) {
                    if (isset($vals[$value]['value'])) {
                        $total += floatval($vals[$value]['value']);
                    }
                }
            }

            // Cek dan proses data periode
            if (isset($index['PERIODE']) && !empty($index['PERIODE'])) {
                $periode = $index['PERIODE'];
                foreach ($periode as $key => $value) {
                    if (isset($vals[$value]['value'])) {
                        $per .= $vals[$value]['value'] . ",";
                    }
                }
            }

            // Cek status pembayaran
            if ($total <= 0) {
                return [
                    'status' => 'success',
                    'message' => "<div class='alert alert-success' style='background: #d4edda; color: #155724; border-color: #c3e6cb;'>Terima kasih, nomor langganan <strong>{$customerId}</strong> tidak ada tagihan yang harus dibayar.</div>"
                ];
            }

            // Generate tampilan HTML seperti sistem lama
            $hasils = "<div class='table-responsive' style='border: 1px solid #fca5a5; background: #fff;'>";
            $hasils .= "<table class='table table-bordered'>";
            
            // Total tagihan
            $hasils .= "<tr style='background: #fee2e2; color: #991b1b;'>";
            $hasils .= "<td colspan='2' class='font-weight-bold text-center' style='text-align: center; font-weight: 700; border-bottom: 1px solid #fca5a5;'>TOTAL TAGIHAN</td>";
            $hasils .= "</tr>";
            $hasils .= "<tr style='background: #fef2f2; color: #b91c1c;'>";
            $hasils .= "<td class='font-weight-bold' style='font-weight: 600; border-bottom: 1px solid #fca5a5;'>Total Tagihan</td>";
            $hasils .= "<td class='font-weight-bold' style='font-weight: 700; font-size: 1.1rem; border-bottom: 1px solid #fca5a5;'> Rp " . number_format($total, 0, ',', '.') . "</td>";
            $hasils .= "</tr>";

            $hasils .= $this->generateBillingInfo($vals, $index, $periode, $dt, $total, $per);

            $hasils .= "</table></div>";

            return [
                'status' => 'success',
                'message' => $hasils
            ];

        } catch (Exception $e) {
            Log::error('SOAP Error: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => "<div class='alert alert-danger' style='background: #fee2e2; color: #b91c1c; border-color: #fca5a5;'><i class='fas fa-exclamation-circle'></i> " . $e->getMessage() . "</div>"
            ];
        }
    }

    private function generateBillingInfo($vals, $index, $periode, $dt, $total, $per)
    {
        $html = "";

        // Header Informasi Pelanggan
        $html .= "<tr style='background: #fee2e2; color: #991b1b;'>";
        $html .= "<td colspan='2' class='font-weight-bold text-center' style='text-align: center; font-weight: 700; border-bottom: 1px solid #fca5a5; border-top: 2px solid #fca5a5;'>INFORMASI PELANGGAN</td>";
        $html .= "</tr>";

        // Tambahkan informasi pelanggan
        $html .= $this->generateCustomerInfo($vals, $index);

        // Header Informasi Tagihan
        $html .= "<tr style='background: #fee2e2; color: #991b1b;'>";
        $html .= "<td colspan='2' class='font-weight-bold text-center' style='text-align: center; font-weight: 700; border-bottom: 1px solid #fca5a5; border-top: 2px solid #fca5a5;'>DETAIL TAGIHAN</td>";
        $html .= "</tr>";

        // Generate informasi per periode
        $periode_array = array_filter(explode(',', rtrim($per, ',')));
        foreach ($periode_array as $key => $period) {
            $period = trim($period);
            if (!empty($period)) {
                $html .= $this->generatePeriodInfo($period, $key, $vals, $index, $dt);

                if ($key < count($periode_array) - 1) {
                    $html .= "<tr><td colspan='2' class='border-bottom'></td></tr>";
                }
            }
        }

        // Informasi pembayaran yang ringkas
        $html .= "<tr>";
        $html .= "<td colspan='2' class='text-muted pt-2'><small>";
        $html .= "Pembayaran melalui Bank BPD Jateng, BSI, BRI, BNI, DANA, GoPay, ShopeePay, ";
        $html .= "Indomaret, Alfamart, Kantor Pos, BPRS, BKK Purbalingga dan Loket Pembayaran Lainnya).<br>";
        $html .= "<strong>Simpan bukti pembayaran untuk keperluan verifikasi jika diperlukan.</strong>";
        $html .= "</small></td></tr>";

        return $html;
    }

    private function generateCustomerInfo($vals, $index)
    {
        $html = "";

        $html .= "<tr><td>Nomor Pelanggan</td><td> " .
            (isset($vals[57]['value']) ? $vals[57]['value'] : '-') . "</td></tr>";

        if (isset($vals[59]['value'])) {
            $nama = $vals[59]['value'];
            $nama_parts = explode(' ', $nama);
            $nama_masked = array_map(function ($part) {
                if (strlen($part) <= 2) {
                    return $part;
                }
                $last_char = substr($part, -1);
                $prefix = substr($part, 0, 2);
                $middle_length = strlen($part) - 3;
                return $prefix . str_repeat('*', $middle_length) . $last_char;
            }, $nama_parts);
            $html .= "<tr><td>Nama</td><td>" . implode(' ', $nama_masked) . "</td></tr>";
        }

        $html .= "<tr><td>Alamat</td><td> " .
            (isset($vals[58]['value']) ? $vals[58]['value'] : '-') . "</td></tr>";

        if (isset($index['STATUS']) && isset($vals[$index['STATUS'][0]])) {
            $status_code = $vals[$index['STATUS'][0]]['value'];
            $status_info = $this->getStatusInfo($status_code);
            $html .= "<tr><td>Status</td><td> <span class='" .
                $status_info['class'] . "'>" . $status_info['description'] . "</span></td></tr>";
        }

        return $html;
    }

    private function generatePeriodInfo($period, $key, $vals, $index, $dt)
    {
        $html = "";
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        $year = substr($period, 0, 4);
        $month = substr($period, 4, 2);
        $formatted_period = (isset($months[$month]) ? $months[$month] : '') . ' ' . $year;

        $html .= "<tr><td>Periode " . ($key + 1) . "</td>";
        $html .= "<td> " . $formatted_period . "</td></tr>";

        if (isset($index['M3'][$key]) && isset($vals[$index['M3'][$key]]['value'])) {
            $m3_value = $vals[$index['M3'][$key]]['value'];
            $html .= "<tr><td>Pemakaian Air</td><td> " . $m3_value . " m³</td></tr>";
        }

        if (isset($dt[$key]) && isset($vals[$dt[$key]]['value'])) {
            $tagihan = floatval($vals[$dt[$key]]['value']);
            $html .= "<tr><td>Tagihan</td><td> Rp " .
                number_format($tagihan, 0, ',', '.') . "</td></tr>";
        }

        return $html;
    }

    private function getStatusInfo($status_code)
    {
        $status_map = [
            '1' => ['description' => 'Baru', 'class' => 'text-primary'],
            '2' => ['description' => 'Aktif', 'class' => 'text-success'],
            '3' => ['description' => 'Tutup Sementara', 'class' => 'text-warning'],
            '4' => ['description' => 'Tutup', 'class' => 'text-danger'],
            '5' => ['description' => 'Bongkar', 'class' => 'text-danger']
        ];

        return isset($status_map[$status_code])
            ? $status_map[$status_code]
            : ['description' => 'Tidak Diketahui', 'class' => 'text-secondary'];
    }
}
