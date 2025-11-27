{{-- resources/views/laporan/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-3">
                    <span class="text-4xl">📊</span>
                    <span
                        class="bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        Laporan Prediksi Harga Emas
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-2">Analisis komprehensif hasil prediksi menggunakan Metode Prophet</p>
            </div>
            <a href="{{ route('laporan.download-pdf') }}"
                class="group relative bg-gradient-to-r from-gray-700 to-gray-800 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3 border border-yellow-500 border-opacity-30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span>Download PDF</span>
            </a>
        </div>

        {{-- Ringkasan Hasil Prediksi --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                    <span class="text-3xl">💰</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Ringkasan Hasil Prediksi</h3>
                    <p class="text-sm text-gray-400">Proyeksi harga emas periode mendatang</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Harga Tertinggi --}}
                <div
                    class="group relative bg-gradient-to-br from-yellow-500 to-yellow-600 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-black opacity-10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-yellow-100 text-sm font-bold tracking-wide uppercase mb-2">Harga Tertinggi</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaTertinggi, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-yellow-100">Proyeksi maksimum</div>
                    </div>
                </div>

                {{-- Harga Terendah --}}
                <div
                    class="group relative bg-gradient-to-br from-gray-700 to-gray-800 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-gray-600">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-gray-300 text-sm font-bold tracking-wide uppercase mb-2">Harga Terendah</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaTerendah, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-400">Proyeksi minimum</div>
                    </div>
                </div>

                {{-- Harga Rata-rata --}}
                <div
                    class="group relative bg-gradient-to-br from-yellow-600 to-yellow-700 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-black opacity-10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-yellow-100 text-sm font-bold tracking-wide uppercase mb-2">Harga Rata-rata</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaRataRata, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-yellow-100">Proyeksi rata-rata</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrik Akurasi Model --}}
        @if ($akurasi)
            <div
                class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                        <span class="text-3xl">🎯</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Tingkat Akurasi Model</h3>
                        <p class="text-sm text-gray-400">Evaluasi performa prediksi Prophet</p>
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
                        class="group relative bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-yellow-500 border-opacity-30">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-5 rounded-full -mr-16 -mt-16">
                        </div>
                        <div class="relative z-10">
                            <div class="text-yellow-400 text-sm font-bold tracking-wide uppercase mb-2">R²</div>
                            <div class="text-4xl font-black text-white mb-2">{{ number_format($akurasi->r_squared, 4) }}
                            </div>
                            <div class="text-xs text-gray-400">Coefficient of Determination</div>
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

        {{-- Grafik Tren Jangka Panjang --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                    <span class="text-3xl">📈</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Tren Harga Jangka Panjang</h3>
                    <p class="text-sm text-gray-400">Proyeksi harga rata-rata per tahun</p>
                </div>
            </div>
            <div class="relative bg-gray-800 bg-opacity-30 rounded-xl p-4 border border-gray-700">
                <canvas id="chartTrend" height="80"></canvas>
            </div>
        </div>

        {{-- Tabel Perbandingan --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl border border-yellow-500 border-opacity-20">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                        <span class="text-3xl">📋</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Tabel Prediksi Per Tahun</h3>
                        <p class="text-sm text-gray-400">Rata-rata harga dan perubahan tahunan</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800 bg-opacity-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Tahun
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Harga Rata-rata (IDR/gram)
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Perubahan dari Tahun Sebelumnya
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @foreach ($trendData as $index => $item)
                                <tr class="hover:bg-gray-800 hover:bg-opacity-30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-semibold">
                                        {{ $item->tahun }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-yellow-400">
                                        Rp {{ number_format($item->harga_rata, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($index > 0)
                                            @php
                                                $perubahan =
                                                    (($item->harga_rata - $trendData[$index - 1]->harga_rata) /
                                                        $trendData[$index - 1]->harga_rata) *
                                                    100;
                                            @endphp
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold {{ $perubahan >= 0 ? 'bg-yellow-500 bg-opacity-20 text-yellow-400' : 'bg-gray-600 bg-opacity-50 text-gray-300' }}">
                                                {{ $perubahan >= 0 ? '↑' : '↓' }}
                                                {{ number_format(abs($perubahan), 2) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const trendData = @json($trendData);

        // Fungsi format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        const ctx = document.getElementById('chartTrend').getContext('2d');

        // Gradient untuk bar chart - Gold theme
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(234, 179, 8, 0.8)');
        gradient.addColorStop(1, 'rgba(161, 98, 7, 0.3)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: trendData.map(item => item.tahun),
                datasets: [{
                    label: 'Harga Rata-rata per Tahun',
                    data: trendData.map(item => item.harga_rata),
                    backgroundColor: gradient,
                    borderColor: 'rgb(234, 179, 8)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
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
                                return 'Harga: ' + formatRupiah(context.parsed.y);
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
                                size: 12,
                                weight: 'bold',
                                family: 'Inter'
                            },
                            color: '#9ca3af',
                            padding: 12,
                            callback: function(value) {
                                return formatRupiah(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: 'Inter'
                            },
                            color: '#9ca3af',
                            padding: 12
                        }
                    }
                }
            }
        });
    </script>
@endpush
