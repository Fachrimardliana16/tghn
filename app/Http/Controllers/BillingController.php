<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BillingSoapService;
use Illuminate\Support\Facades\Cache;

class BillingController extends Controller
{
    protected $billingService;

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
        // Validasi input nomor pelanggan harus 8 digit angka
        if (!$request->has('nolangg') || !preg_match('/^\d{8}$/', $request->input('nolangg'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Pelanggan harus berupa 8 digit angka.'
            ]);
        }

        $customerId = $request->input('nolangg');
        $cacheKey = 'billing_' . $customerId;

        try {
            // Implementasi Caching response SOAP (Durasi 5 menit = 300 detik)
            $billingData = Cache::remember($cacheKey, 300, function () use ($customerId) {
                return $this->billingService->checkBilling($customerId);
            });

            return response()->json($billingData);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
