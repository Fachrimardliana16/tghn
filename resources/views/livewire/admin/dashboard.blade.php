<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Admin</h1>
                    <p class="text-gray-600">Monitor aktivitas pengecekan tagihan secara real-time</p>
                    <p class="text-sm text-gray-500 mt-1" id="currentDateTime"></p>
                </div>
                <div class="flex gap-3">
                    <button wire:click="refresh" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl hover:from-red-700 hover:to-red-800 transition-all shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 text-green-800 px-6 py-4 rounded-xl shadow-md" role="alert">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Pengecekan</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($totalChecks) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pengecekan Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($todayChecks) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-4 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100 hover:shadow-2xl transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pelanggan Unik</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($uniqueCustomers) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-4 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-blue-500 to-purple-500 w-1 h-6 rounded-full mr-3"></span>
                    Distribusi Status
                </h3>
                <div style="height: 280px;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Peak Hours Chart -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-green-500 to-teal-500 w-1 h-6 rounded-full mr-3"></span>
                    Jam Sibuk (Peak Hours)
                </h3>
                <div style="height: 280px;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Device Analysis & Trends -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Device Analysis Pie Chart -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-yellow-500 to-orange-500 w-1 h-6 rounded-full mr-3"></span>
                    Analisa Device
                </h3>
                <div style="height: 280px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>

            <!-- 7 Days Trend -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <span class="bg-gradient-to-r from-pink-500 to-red-500 w-1 h-6 rounded-full mr-3"></span>
                    Trend 7 Hari Terakhir
                </h3>
                <div style="height: 280px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Customers Table -->
        <div class="bg-white rounded-2xl shadow-xl mb-8 border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="bg-gradient-to-r from-indigo-500 to-blue-500 w-1 h-6 rounded-full mr-3"></span>
                    Top 10 Pelanggan Aktif
                </h3>
                <p class="mt-2 text-sm text-gray-500">Pelanggan dengan pengecekan terbanyak</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Langganan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Jumlah Cek</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Total Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topCustomers as $index => $customer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full font-bold text-sm shadow-md
                                        {{ $index == 0 ? 'bg-gradient-to-br from-yellow-400 to-yellow-600 text-white' :
                                           ($index == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-500 text-white' :
                                           ($index == 2 ? 'bg-gradient-to-br from-orange-400 to-orange-600 text-white' :
                                           'bg-gradient-to-br from-blue-400 to-blue-600 text-white')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $customer->nolang }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->nama_pelanggan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">{{ number_format($customer->check_count) }}x</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($customer->total_tagihan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Checks -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <span class="bg-gradient-to-r from-teal-500 to-green-500 w-1 h-6 rounded-full mr-3"></span>
                    Pengecekan Terbaru
                </h3>
                <p class="mt-2 text-sm text-gray-500">10 pengecekan terakhir</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No. Langganan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tagihan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentChecks as $check)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $check->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $check->nolang }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $check->nama_pelanggan ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm
                                        {{ str_contains(strtolower($check->status), 'lunas') ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'bg-gradient-to-r from-red-400 to-red-600 text-white' }}">
                                        {{ $check->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($check->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $check->ip_address ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Update current date and time
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    };
    const dateTimeString = now.toLocaleDateString('id-ID', options);
    document.getElementById('currentDateTime').textContent = dateTimeString;
}

updateDateTime();
setInterval(updateDateTime, 1000);

let statusChart, peakHoursChart, deviceChart, trendChart;

function initCharts() {
    // Destroy existing charts
    if (statusChart) statusChart.destroy();
    if (peakHoursChart) peakHoursChart.destroy();
    if (deviceChart) deviceChart.destroy();
    if (trendChart) trendChart.destroy();
    // Status Distribution Bar Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        statusChart = new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($statusDistribution->pluck('status')) !!},
                datasets: [{
                    label: 'Jumlah',
                    data: {!! json_encode($statusDistribution->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(251, 191, 36, 1)',
                        'rgba(168, 85, 247, 1)',
                        'rgba(34, 197, 94, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Peak Hours Line Chart
    const peakCtx = document.getElementById('peakHoursChart');
    if (peakCtx) {
        peakHoursChart = new Chart(peakCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($hourlyDistribution->pluck('hour')) !!},
                datasets: [{
                    label: 'Pengecekan',
                    data: {!! json_encode($hourlyDistribution->pluck('total')) !!},
                    borderColor: 'rgba(34, 197, 94, 1)',
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'rgba(34, 197, 94, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Device Analysis Pie Chart
    const deviceCtx = document.getElementById('deviceChart');
    if (deviceCtx) {
        deviceChart = new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($deviceAnalysis->pluck('device')) !!},
                datasets: [{
                    data: {!! json_encode($deviceAnalysis->pluck('total')) !!},
                    backgroundColor: [
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ],
                    borderColor: [
                        'rgba(251, 191, 36, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(168, 85, 247, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    }

    // 7 Days Trend Chart
    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($checkTrends->map(fn($t) => \Carbon\Carbon::parse($t->date)->format('d M'))) !!},
                datasets: [{
                    label: 'Pengecekan',
                    data: {!! json_encode($checkTrends->pluck('total')) !!},
                    borderColor: 'rgba(236, 72, 153, 1)',
                    backgroundColor: 'rgba(236, 72, 153, 0.2)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(236, 72, 153, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'rgba(236, 72, 153, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initCharts();
});

// Re-init charts after Livewire updates
document.addEventListener('livewire:navigated', function() {
    initCharts();
});

window.addEventListener('livewire:init', () => {
    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
        succeed(({ snapshot, effect }) => {
            queueMicrotask(() => {
                initCharts();
            });
        });
    });
});
</script>
