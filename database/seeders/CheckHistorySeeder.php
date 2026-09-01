<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckHistory;
use Illuminate\Support\Facades\DB;

class CheckHistorySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $statuses = [
            'Lunas',
            'Belum Lunas',
            'Menunggak 1 Bulan',
            'Menunggak 2 Bulan',
            'Menunggak 3 Bulan',
        ];

        $userAgents = [
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148',
        ];

        $ipAddresses = [
            '192.168.1.1',
            '192.168.1.2',
            '10.0.0.1',
            '172.16.0.1',
            '203.0.113.1',
        ];

        // Generate data untuk 30 hari terakhir
        for ($day = 30; $day >= 0; $day--) {
            // Random jumlah pengecekan per hari (5-50)
            $checksPerDay = rand(5, 50);

            for ($i = 0; $i < $checksPerDay; $i++) {
                $nolang = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
                $status = $statuses[array_rand($statuses)];
                $totalTagihan = $status === 'Lunas' ? 0 : rand(50000, 500000);

                CheckHistory::create([
                    'nolang' => $nolang,
                    'status' => $status,
                    'nama_pelanggan' => 'Pelanggan ' . $nolang,
                    'total_tagihan' => $totalTagihan,
                    'ip_address' => $ipAddresses[array_rand($ipAddresses)],
                    'user_agent' => $userAgents[array_rand($userAgents)],
                    'created_at' => now()->subDays($day)->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                    'updated_at' => now()->subDays($day)->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                ]);
            }
        }

        $this->command->info('Check history data seeded successfully!');
    }
}
