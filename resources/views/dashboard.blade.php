@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-3">
                    <span class="text-4xl">💰</span>
                    <span
                        class="bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        Dashboard Harga Emas
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-2">
                    Monitor dan analisis harga emas real-time
                    @php
                        $dayOfWeek = now()->dayOfWeek;
                        $isWeekend = in_array($dayOfWeek, [0, 6]); // 0 = Sunday, 6 = Saturday
                    @endphp
                    @if ($isWeekend)
                        <span
                            class="inline-flex items-center gap-1 ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            Pasar Tutup (Weekend)
                        </span>
                    @endif
                </p>
            </div>
            <button id="btnUpdateHarga" onclick="updateHargaEmas()"
                class="group relative bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                <svg id="iconUpdate" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                <span id="textUpdate">Update Harga</span>
            </button>
        </div>

        {{-- Alert Messages dengan Animasi --}}
        @if (session('success'))
            <div
                class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4 rounded-xl shadow-lg animate-slide-down flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div
                class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-lg animate-slide-down flex items-center gap-3">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Kartu Statistik dengan Tema Gold & Black --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Harga Hari Ini --}}
            <div
                class="group relative bg-gradient-to-br from-yellow-500 via-yellow-600 to-yellow-700 overflow-hidden shadow-2xl rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-yellow-500/50">
                <div class="absolute top-0 right-0 w-40 h-40 bg-black opacity-5 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-black opacity-5 rounded-full -ml-16 -mb-16"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-white text-sm font-bold tracking-wide uppercase opacity-90">Harga Hari Ini</div>
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <span class="text-2xl">✨</span>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-white mb-2">
                        Rp {{ $hargaHariIni ? number_format($hargaHariIni->harga_idr, 0, ',', '.') : '0' }}
                    </div>
                    <div class="text-sm text-yellow-100 font-semibold mb-1">
                        {{ $hargaHariIni ? $hargaHariIni->tanggal->format('d M Y') : '-' }}
                    </div>
                    @if ($hargaHariIni)
                        @php
                            $dataDate = $hargaHariIni->tanggal;
                            $daysDiff = now()->diffInDays($dataDate);
                        @endphp
                        @if ($daysDiff > 0)
                            <div class="text-xs text-yellow-200 mb-3 opacity-90">
                                ({{ $daysDiff }} hari yang lalu)
                            </div>
                        @else
                            <div class="text-xs text-yellow-200 mb-3 opacity-90">
                                (Data Terkini)
                            </div>
                        @endif
                    @else
                        <div class="mb-4"></div>
                    @endif
                    <div
                        class="bg-black bg-opacity-30 rounded-xl px-4 py-3 backdrop-blur-sm border border-white border-opacity-20">
                        <div class="text-xs text-yellow-100 mb-1 font-semibold">Harga USD</div>
                        <div class="text-lg font-bold text-white">
                            ${{ $hargaHariIni ? number_format($hargaHariIni->harga_usd, 2) : '0.00' }}/oz
                        </div>
                    </div>
                </div>
            </div>

            {{-- Harga Bulan Lalu --}}
            <div
                class="group relative bg-gradient-to-br from-gray-800 via-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-gray-900/50">
                <div class="absolute top-0 right-0 w-40 h-40 bg-yellow-500 opacity-5 rounded-full -mr-20 -mt-20"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-gray-300 text-sm font-bold tracking-wide uppercase">Harga Bulan Lalu</div>
                        <div class="bg-yellow-500 bg-opacity-20 rounded-full p-2">
                            <span class="text-2xl">📅</span>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-white mb-2">
                        Rp {{ $hargaBulanLalu ? number_format($hargaBulanLalu->harga_idr, 0, ',', '.') : '0' }}
                    </div>
                    <div class="text-sm text-gray-400 font-semibold">
                        {{ $hargaBulanLalu ? $hargaBulanLalu->tanggal->format('d M Y') : '-' }}
                    </div>
                </div>
            </div>

            {{-- Perubahan --}}
            <div
                class="group relative bg-gradient-to-br {{ $perubahanPersen >= 0 ? 'from-yellow-500 via-yellow-600 to-yellow-700' : 'from-gray-800 via-gray-900 to-black' }} overflow-hidden shadow-2xl rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 {{ $perubahanPersen >= 0 ? 'hover:shadow-yellow-500/50' : 'hover:shadow-gray-900/50' }}">
                <div
                    class="absolute top-0 right-0 w-40 h-40 {{ $perubahanPersen >= 0 ? 'bg-black' : 'bg-yellow-500' }} opacity-5 rounded-full -mr-20 -mt-20">
                </div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="{{ $perubahanPersen >= 0 ? 'text-white' : 'text-gray-300' }} text-sm font-bold tracking-wide uppercase">
                            Perubahan</div>
                        <div
                            class="{{ $perubahanPersen >= 0 ? 'bg-black' : 'bg-yellow-500' }} bg-opacity-20 rounded-full p-2">
                            <span class="text-2xl">{{ $perubahanPersen >= 0 ? '📈' : '📉' }}</span>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ $perubahanPersen >= 0 ? '+' : '' }}{{ number_format($perubahanPersen, 2) }}%
                    </div>
                    <div
                        class="flex items-center gap-2 text-sm font-semibold {{ $perubahanPersen >= 0 ? 'text-yellow-100' : 'text-gray-400' }}">
                        <span class="text-2xl">{{ $perubahanPersen >= 0 ? '▲' : '▼' }}</span>
                        <span>Dalam 1 Bulan</span>
                    </div>
                </div>
            </div>

            {{-- Total Data --}}
            <div
                class="group relative bg-gradient-to-br from-gray-800 via-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-6 transform hover:scale-105 transition-all duration-300 hover:shadow-gray-900/50 border border-yellow-500 border-opacity-20">
                <div class="absolute top-0 right-0 w-40 h-40 bg-yellow-500 opacity-5 rounded-full -mr-20 -mt-20"></div>

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-gray-300 text-sm font-bold tracking-wide uppercase">Total Data</div>
                        <div class="bg-yellow-500 bg-opacity-20 rounded-full p-2">
                            <span class="text-2xl">📊</span>
                        </div>
                    </div>
                    <div class="text-4xl font-black text-white mb-2">
                        {{ number_format($totalData) }}
                    </div>
                    <div class="text-sm text-gray-400 font-semibold mb-4">
                        Data Historis
                    </div>
                    <div
                        class="bg-yellow-500 bg-opacity-10 rounded-xl px-4 py-3 backdrop-blur-sm border border-yellow-500 border-opacity-30">
                        <div class="text-xs text-yellow-400 mb-1 font-semibold">Prediksi</div>
                        <div class="text-lg font-bold text-yellow-500">
                            {{ number_format($totalPrediksi) }} Data
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Grafik Tren Harga dengan Tema Elegant --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center justify-between mb-8 flex-wrap gap-4">
                <div>
                    <h3 class="text-2xl font-bold text-white flex items-center gap-3 mb-2">
                        <div class="bg-yellow-500 bg-opacity-20 rounded-full p-2">
                            <span class="text-2xl">📈</span>
                        </div>
                        <span class="bg-gradient-to-r from-yellow-400 to-yellow-600 bg-clip-text text-transparent">
                            Tren Harga Emas
                        </span>
                    </h3>
                    <p class="text-sm text-gray-400">Pergerakan harga dalam 1 tahun terakhir</p>
                </div>
                <div class="flex gap-2">
                    <button
                        class="px-5 py-2.5 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg text-sm font-bold hover:from-yellow-600 hover:to-yellow-700 transition-all shadow-lg">
                        1Y
                    </button>
                    <button
                        class="px-5 py-2.5 bg-gray-800 text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-700 transition-all border border-gray-700">
                        6M
                    </button>
                    <button
                        class="px-5 py-2.5 bg-gray-800 text-gray-300 rounded-lg text-sm font-bold hover:bg-gray-700 transition-all border border-gray-700">
                        3M
                    </button>
                </div>
            </div>
            <div class="relative bg-gray-800 bg-opacity-30 rounded-xl p-4 border border-gray-700">
                <canvas id="chartHargaEmas" height="80"></canvas>
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

        /* Animasi untuk kartu statistik */
        .group:hover {
            animation: pulse-glow 2s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            50% {
                box-shadow: 0 25px 50px -12px rgba(234, 179, 8, 0.25);
            }
        }

        /* Loading spinner animation */
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Responsiveness untuk mobile */
        @media (max-width: 768px) {
            .text-4xl {
                font-size: 2rem;
            }

            .text-3xl {
                font-size: 1.75rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk grafik
        const dataGrafik = @json($dataGrafik);

        const ctx = document.getElementById('chartHargaEmas').getContext('2d');

        // Gradient untuk chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(234, 179, 8, 0.3)');
        gradient.addColorStop(1, 'rgba(234, 179, 8, 0.01)');

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataGrafik.map(item => item.tanggal),
                datasets: [{
                    label: 'Harga Emas (IDR)',
                    data: dataGrafik.map(item => item.harga_idr),
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: gradient,
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgb(234, 179, 8)',
                    pointBorderColor: '#1f2937',
                    pointBorderWidth: 3,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgb(234, 179, 8)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
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
                                return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
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
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    compactDisplay: 'short'
                                }).format(value);
                            },
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: 'Inter'
                            },
                            color: '#9ca3af',
                            padding: 12
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 12,
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

        // Fungsi update harga emas dengan loading state yang diperbaiki
        function updateHargaEmas() {
            if (!confirm('Apakah Anda yakin ingin memperbarui harga emas dari Yahoo Finance?')) return;

            const button = document.getElementById('btnUpdateHarga');
            const iconUpdate = document.getElementById('iconUpdate');
            const textUpdate = document.getElementById('textUpdate');

            // Simpan konten asli
            const originalIcon = iconUpdate.innerHTML;
            const originalText = textUpdate.textContent;

            // Tampilkan loading state
            button.disabled = true;
            button.classList.remove('hover:-translate-y-1');
            button.classList.add('opacity-75', 'cursor-not-allowed');

            iconUpdate.innerHTML = `
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        `;
            iconUpdate.classList.add('animate-spin');
            textUpdate.textContent = 'Memperbarui...';

            // Log untuk debugging
            console.log('Mengirim request ke:', '{{ route('dashboard.update-harga') }}');
            console.log('CSRF Token:', '{{ csrf_token() }}');

            fetch('{{ route('dashboard.update-harga') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));

                    // Cek apakah response adalah JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Server tidak mengembalikan JSON response. Cek route dan controller.');
                    }

                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    console.log('Full response:', JSON.stringify(data, null, 2));

                    if (data.success) {
                        // Sukses - reload halaman
                        textUpdate.textContent = 'Berhasil!';
                        iconUpdate.classList.remove('animate-spin');

                        // Build alert message yang informatif
                        let alertMessage = '✅ Data Harga Emas Berhasil Diperbarui!\n';
                        alertMessage += '━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n';

                        // Cek apakah ada data
                        if (data.data && data.data.after) {
                            const afterData = data.data.after;

                            // Informasi tanggal dan hari
                            if (afterData.latest_date) {
                                const latestDate = new Date(afterData.latest_date + 'T00:00:00');
                                const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                                const dayName = dayNames[latestDate.getDay()];

                                alertMessage += '\n📊 INFORMASI DATA:\n';
                                alertMessage += `📅 Tanggal: ${afterData.latest_date}\n`;
                                alertMessage += `🗓️  Hari: ${dayName}\n`;

                                // Harga
                                if (afterData.latest_price) {
                                    const priceIDR = parseFloat(afterData.latest_price);
                                    alertMessage +=
                                        `💰 Harga: Rp ${priceIDR.toLocaleString('id-ID', {maximumFractionDigits: 0})}\n`;
                                }

                                if (afterData.latest_price_usd) {
                                    const priceUSD = parseFloat(afterData.latest_price_usd);
                                    alertMessage +=
                                        `💵 Harga: ${priceUSD.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}/oz\n`;
                                }

                                // Selisih hari
                                const diffDays = data.data.days_diff || 0;
                                if (diffDays > 0) {
                                    alertMessage += `⏱️  Selisih: ${diffDays} hari yang lalu\n`;
                                } else {
                                    alertMessage += `✅ Status: Data hari ini tersedia!\n`;
                                }

                                // Info new records
                                const newRecords = data.data.new_records || 0;
                                alertMessage += `\n📈 STATISTIK:\n`;
                                alertMessage += `📊 Total Data: ${afterData.count || 0}\n`;

                                if (newRecords > 0) {
                                    alertMessage += `✨ Data Baru: +${newRecords} record\n`;
                                } else {
                                    alertMessage += `✓ Status: Database up-to-date\n`;
                                }

                                // Cek weekend
                                if (data.data.today && data.data.today.is_weekend && diffDays > 0) {
                                    const todayDayOfWeek = data.data.today.day_of_week || 0;
                                    const todayDayName = dayNames[todayDayOfWeek];

                                    alertMessage += `\n⚠️  INFO PASAR:\n`;
                                    alertMessage += `📌 Hari ini: ${todayDayName} (Weekend)\n`;
                                    alertMessage += `🚫 Status: Pasar TUTUP\n`;
                                    alertMessage += `ℹ️  Data terbaru adalah hari kerja terakhir\n`;
                                } else if (diffDays > 0 && diffDays <= 3) {
                                    alertMessage += `\nℹ️  Data mungkin delay dari Yahoo Finance\n`;
                                }
                            }
                        } else {
                            alertMessage += '\n✓ Proses update selesai\n';
                        }

                        alertMessage += '\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━';

                        alert(alertMessage);

                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    } else {
                        // Gagal - kembalikan tombol
                        alert('❌ GAGAL MEMPERBARUI HARGA\n\n' + data.message);
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    // Error - kembalikan tombol
                    alert('⚠️ Terjadi kesalahan:\n' + error.message + '\n\nSilakan cek console untuk detail.');
                    resetButton();
                });

            function resetButton() {
                button.disabled = false;
                button.classList.add('hover:-translate-y-1');
                button.classList.remove('opacity-75', 'cursor-not-allowed');
                iconUpdate.innerHTML = originalIcon;
                iconUpdate.classList.remove('animate-spin');
                textUpdate.textContent = originalText;
            }
        }
    </script>
@endpush
