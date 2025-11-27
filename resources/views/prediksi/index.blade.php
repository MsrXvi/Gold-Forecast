@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-3">
                    <span class="text-4xl">🔮</span>
                    <span
                        class="bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        Prediksi Harga Emas
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-2">Analisis dan Peramalan menggunakan Metode Prophet</p>
            </div>
            <a href="{{ route('prediksi.create') }}"
                class="group relative bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Prediksi Baru</span>
            </a>
        </div>

        {{-- Alert Messages --}}
        @if (session('success'))
            <div
                class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-4 rounded-xl shadow-lg animate-slide-down flex items-center gap-3 border border-yellow-400">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Metrik Akurasi dengan Desain Modern --}}
        @if ($akurasi)
            <div
                class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                        <span class="text-3xl">📊</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Akurasi Metode Prophet</h3>
                        <p class="text-sm text-gray-400">Evaluasi performa prediksi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    {{-- MAPE --}}
                    <div
                        class="group relative bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-yellow-500 border-opacity-30">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-5 rounded-full -mr-16 -mt-16">
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-400 text-sm font-bold tracking-wide uppercase mb-2">MAPE</div>
                            <div class="text-4xl font-black text-white mb-2">{{ number_format($akurasi->mape, 2) }}%</div>
                            <div class="text-xs text-gray-400">Mean Absolute Percentage Error</div>
                        </div>
                    </div>

                    {{-- RMSE --}}
                    <div
                        class="group relative bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-yellow-500 border-opacity-30">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-5 rounded-full -mr-16 -mt-16">
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-400 text-sm font-bold tracking-wide uppercase mb-2">RMSE</div>
                            <div class="text-4xl font-black text-white mb-2">{{ number_format($akurasi->rmse, 2) }}</div>
                            <div class="text-xs text-gray-400">Root Mean Square Error</div>
                        </div>
                    </div>

                    {{-- MAE --}}
                    <div
                        class="group relative bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-yellow-500 border-opacity-30">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-5 rounded-full -mr-16 -mt-16">
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-400 text-sm font-bold tracking-wide uppercase mb-2">MAE</div>
                            <div class="text-4xl font-black text-white mb-2">{{ number_format($akurasi->mae, 2) }}</div>
                            <div class="text-xs text-gray-400">Mean Absolute Error</div>
                        </div>
                    </div>

                    {{-- R² --}}
                    <div
                        class="group relative bg-gradient-to-br from-yellow-500 to-yellow-600 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-black opacity-10 rounded-full -mr-16 -mt-16"></div>
                        <div class="relative z-10">
                            <div class="text-yellow-100 text-sm font-bold tracking-wide uppercase mb-2">R²</div>
                            <div class="text-4xl font-black text-white mb-2">{{ number_format($akurasi->r_squared, 4) }}
                            </div>
                            <div class="text-xs text-yellow-100">Coefficient of Determination</div>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="bg-yellow-500 bg-opacity-10 rounded-xl px-6 py-4 border border-yellow-500 border-opacity-30">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <div class="text-yellow-400 font-semibold text-sm mb-1">Interpretasi:</div>
                            <div class="text-gray-300 text-sm">{{ $akurasi->keterangan }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Grafik Perbandingan --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                    <span class="text-3xl">📈</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Grafik Harga Aktual vs Prediksi</h3>
                    <p class="text-sm text-gray-400">Perbandingan data historis dan peramalan</p>
                </div>
            </div>
            <div class="relative bg-gray-800 bg-opacity-30 rounded-xl p-4 border border-gray-700">
                <canvas id="chartPrediksi" height="70"></canvas>
            </div>
        </div>

        {{-- Tabel Hasil Prediksi --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl border border-yellow-500 border-opacity-20">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                        <span class="text-3xl">📋</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Data Prediksi Detail</h3>
                        <p class="text-sm text-gray-400">Menampilkan semua data aktual dan prediksi</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800 bg-opacity-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Tipe
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Harga USD/oz
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Harga IDR/gram
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Rentang Prediksi (IDR)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($prediksiData as $item)
                                <tr class="hover:bg-gray-800 hover:bg-opacity-30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-semibold">
                                        {{ $item->tanggal_prediksi->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->is_aktual)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-600 bg-opacity-50 text-gray-300 border border-gray-500">
                                                Aktual
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-500 bg-opacity-20 text-yellow-400 border border-yellow-500 border-opacity-30">
                                                Prediksi
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-green-400">
                                        ${{ number_format($item->harga_prediksi_usd, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-yellow-400">
                                        Rp {{ number_format($item->harga_prediksi_idr, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                        @if (!$item->is_aktual)
                                            <div class="flex items-center gap-2">
                                                <span class="bg-gray-700 px-2 py-1 rounded">
                                                    Rp
                                                    {{ number_format(($item->lower_bound * 31.1035 * 16650) / 31.1035, 0, ',', '.') }}
                                                </span>
                                                <span>-</span>
                                                <span class="bg-gray-700 px-2 py-1 rounded">
                                                    Rp
                                                    {{ number_format(($item->upper_bound * 31.1035 * 16650) / 31.1035, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <div>
                                                <p class="text-gray-400 font-semibold mb-2">Belum ada data prediksi</p>
                                                <p class="text-gray-500 text-sm">Silakan buat prediksi baru untuk melihat
                                                    hasil forecasting</p>
                                            </div>
                                            <a href="{{ route('prediksi.create') }}"
                                                class="mt-4 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-2 px-6 rounded-lg transition-all">
                                                Buat Prediksi Sekarang
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($prediksiData->count() > 0)
                    <div
                        class="mt-6 flex items-center justify-between px-6 py-4 bg-gray-800 bg-opacity-30 rounded-lg border border-gray-700">
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span>Total <strong class="text-white">{{ $prediksiData->count() }}</strong> data
                                tersedia</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            Semua data ditampilkan untuk analisis lengkap
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        @keyframes slide-down {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-down {
            animation: slide-down 0.4s ease-out;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Ambil data grafik
        fetch('{{ route('prediksi.grafik') }}')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('chartPrediksi').getContext('2d');

                // Konversi USD ke IDR per gram (1 oz = 31.1035 gram, rate USD ke IDR = 16650)
                const kursUSD = 16650; // Sesuaikan dengan kurs terkini
                const gramPerOz = 31.1035;

                // Gradient untuk area - Gold theme
                const gradientHistoris = ctx.createLinearGradient(0, 0, 0, 400);
                gradientHistoris.addColorStop(0, 'rgba(156, 163, 175, 0.3)');
                gradientHistoris.addColorStop(1, 'rgba(156, 163, 175, 0.01)');

                const gradientPrediksi = ctx.createLinearGradient(0, 0, 0, 400);
                gradientPrediksi.addColorStop(0, 'rgba(234, 179, 8, 0.3)');
                gradientPrediksi.addColorStop(1, 'rgba(234, 179, 8, 0.01)');

                // Format tanggal ke d-m-Y
                const formatTanggal = (tanggalStr) => {
                    const date = new Date(tanggalStr);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                };

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [
                            ...data.historis.map(item => formatTanggal(item.tanggal)),
                            ...data.prediksi.map(item => formatTanggal(item.tanggal))
                        ],
                        datasets: [{
                                label: 'Harga Aktual',
                                data: data.historis.map(item => ({
                                    x: formatTanggal(item.tanggal),
                                    y: (item.harga_idr)
                                })),
                                borderColor: 'rgb(156, 163, 175)',
                                backgroundColor: gradientHistoris,
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(156, 163, 175)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true
                            },
                            {
                                label: 'Prediksi',
                                data: data.prediksi.map(item => ({
                                    x: formatTanggal(item.tanggal),
                                    y: (item.harga_idr)
                                })),
                                borderColor: 'rgb(234, 179, 8)',
                                backgroundColor: gradientPrediksi,
                                borderDash: [8, 4],
                                tension: 0.4,
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointBackgroundColor: 'rgb(234, 179, 8)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        size: 14,
                                        weight: 'bold',
                                        family: 'Inter'
                                    },
                                    color: '#d1d5db',
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.9)',
                                padding: 16,
                                titleFont: {
                                    size: 15,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 14
                                },
                                borderColor: 'rgb(234, 179, 8)',
                                borderWidth: 2,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += 'Rp ' + context.parsed.y.toLocaleString('id-ID', {
                                                minimumFractionDigits: 0,
                                                maximumFractionDigits: 0
                                            });
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                grid: {
                                    color: 'rgba(75, 85, 99, 0.3)',
                                    drawBorder: false
                                },
                                title: {
                                    display: true,
                                    text: 'Harga (IDR/gram)',
                                    color: '#9ca3af',
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    }
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: 'bold',
                                        family: 'Inter'
                                    },
                                    color: '#9ca3af',
                                    padding: 12,
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID', {
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        });
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    maxTicksLimit: 10,
                                    font: {
                                        size: 10,
                                        weight: 'bold',
                                        family: 'Inter'
                                    },
                                    color: '#9ca3af',
                                    padding: 12,
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error loading chart:', error);
            });
    </script>
@endpush
