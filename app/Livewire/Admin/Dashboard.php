<?php

namespace App\Livewire\Admin;

use App\Models\CheckHistory;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $layout = 'components.layouts.app';

    public $totalChecks;
    public $todayChecks;
    public $uniqueCustomers;
    public $totalTagihan;
    public $averageTagihan;
    public $statusDistribution;
    public $topCustomers;
    public $checkTrends;
    public $hourlyDistribution;
    public $peakHours;
    public $deviceAnalysis;
    public $recentChecks;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Total pengecekan
        $this->totalChecks = CheckHistory::count();

        // Pengecekan hari ini
        $this->todayChecks = CheckHistory::whereDate('created_at', today())->count();

        // Unique customers
        $this->uniqueCustomers = CheckHistory::distinct('nolang')->count('nolang');

        // Total tagihan
        $this->totalTagihan = CheckHistory::sum('total_tagihan');

        // Rata-rata tagihan
        $this->averageTagihan = CheckHistory::avg('total_tagihan');

        // Distribusi status
        $this->statusDistribution = CheckHistory::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => $item->status,
                    'total' => $item->total,
                    'percentage' => round(($item->total / $this->totalChecks) * 100, 2)
                ];
            });

        // Top 10 customers berdasarkan jumlah pengecekan
        $this->topCustomers = CheckHistory::select('nolang', 'nama_pelanggan', DB::raw('count(*) as check_count'), DB::raw('sum(total_tagihan) as total_tagihan'))
            ->groupBy('nolang', 'nama_pelanggan')
            ->orderByDesc('check_count')
            ->limit(10)
            ->get();

        // Trend pengecekan 7 hari terakhir
        $this->checkTrends = CheckHistory::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Distribusi per jam
        $this->hourlyDistribution = CheckHistory::select(
                DB::raw("CAST(strftime('%H', created_at) as INTEGER) as hour"),
                DB::raw('count(*) as total')
            )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => sprintf('%02d:00', $item->hour),
                    'total' => $item->total
                ];
            });

        // Peak hours
        $peakHourData = CheckHistory::select(
                DB::raw("CAST(strftime('%H', created_at) as INTEGER) as hour"),
                DB::raw('count(*) as total')
            )
            ->groupBy('hour')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $this->peakHours = $peakHourData->map(function ($item) {
            return [
                'hour' => sprintf('%02d:00 - %02d:00', $item->hour, ($item->hour + 1) % 24),
                'total' => $item->total
            ];
        });

        // Device analysis berdasarkan user agent
        $this->deviceAnalysis = CheckHistory::select('user_agent', DB::raw('count(*) as total'))
            ->whereNotNull('user_agent')
            ->groupBy('user_agent')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $device = $this->parseUserAgent($item->user_agent);
                return [
                    'device' => $device,
                    'total' => $item->total,
                    'percentage' => round(($item->total / $this->totalChecks) * 100, 2)
                ];
            });

        // Recent checks
        $this->recentChecks = CheckHistory::latest()
            ->limit(10)
            ->get();
    }

    private function parseUserAgent($userAgent)
    {
        if (stripos($userAgent, 'mobile') !== false) {
            return 'Mobile';
        } elseif (stripos($userAgent, 'tablet') !== false) {
            return 'Tablet';
        } elseif (stripos($userAgent, 'bot') !== false || stripos($userAgent, 'crawler') !== false) {
            return 'Bot/Crawler';
        } else {
            return 'Desktop';
        }
    }

    public function refresh()
    {
        $this->loadDashboardData();
        session()->flash('message', 'Data berhasil di-refresh!');
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('components.layouts.app');
    }
}
