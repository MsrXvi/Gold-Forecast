{{-- resources/views/harga-emas/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-3">
                    <span class="text-4xl">💎</span>
                    <span
                        class="bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        Data Harga Emas Historis
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-2">Data harga emas real-time dari Yahoo Finance</p>
            </div>

            {{-- Button Tambah Data dengan Modal --}}
            <button type="button" onclick="openModal()"
                class="group relative bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Data</span>
            </button>
        </div>

        {{-- Modal Tambah Data --}}
        <div id="tambahDataModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
            <div class="bg-gradient-to-br from-gray-900 to-black rounded-2xl shadow-2xl max-w-md w-full border border-yellow-500 border-opacity-30 animate-modal-show">
                <div class="p-6">
                    {{-- Header Modal --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                                <span class="text-3xl">📊</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-white">Tambah Data Historis</h3>
                                <p class="text-sm text-gray-400">Pilih periode data yang ingin ditambahkan</p>
                            </div>
                        </div>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('harga-emas.refresh') }}" method="POST" id="tambahDataForm">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-300 mb-3">Pilih Periode Data</label>

                            {{-- Pilihan Tahun --}}
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <button type="button" onclick="selectYears(1)"
                                    class="year-option bg-gray-800 hover:bg-yellow-600 border-2 border-gray-700 hover:border-yellow-500 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-105">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="text-lg">1 Tahun</div>
                                </button>
                                <button type="button" onclick="selectYears(3)"
                                    class="year-option bg-gray-800 hover:bg-yellow-600 border-2 border-gray-700 hover:border-yellow-500 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-105">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="text-lg">3 Tahun</div>
                                </button>
                                <button type="button" onclick="selectYears(5)"
                                    class="year-option bg-gray-800 hover:bg-yellow-600 border-2 border-gray-700 hover:border-yellow-500 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-105">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="text-lg">5 Tahun</div>
                                </button>
                                <button type="button" onclick="selectYears(10)"
                                    class="year-option bg-gray-800 hover:bg-yellow-600 border-2 border-gray-700 hover:border-yellow-500 text-white font-bold py-4 px-4 rounded-xl transition-all transform hover:scale-105">
                                    <div class="text-2xl mb-1">📅</div>
                                    <div class="text-lg">10 Tahun</div>
                                </button>
                            </div>

                            {{-- Input Custom --}}
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-400 mb-2">Atau masukkan custom (1-20 tahun)</label>
                                <input type="number"
                                    name="years"
                                    id="yearsInput"
                                    min="1"
                                    max="20"
                                    value="5"
                                    class="w-full bg-gray-800 border-2 border-gray-700 focus:border-yellow-500 rounded-lg px-4 py-3 text-white text-lg font-bold focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition-all"
                                    placeholder="Masukkan jumlah tahun">
                                <div class="absolute right-3 top-11 text-gray-500 font-bold">Tahun</div>
                            </div>
                        </div>

                        {{-- Info Box --}}
                        <div class="bg-yellow-500 bg-opacity-10 border border-yellow-500 border-opacity-30 rounded-lg p-4 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-gray-300">
                                    <p class="font-bold text-yellow-400 mb-1">Informasi:</p>
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Data diambil dari Yahoo Finance</li>
                                        <li>Proses mungkin memakan waktu beberapa menit</li>
                                        <li>Data duplikat akan otomatis diupdate</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-3">
                            <button type="button" onclick="closeModal()"
                                class="flex-1 bg-gray-700 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all">
                                Batal
                            </button>
                            <button type="submit" id="submitBtn"
                                class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-3 px-6 rounded-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                <span>Tambah Data</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

        @if (session('error'))
            <div
                class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 rounded-xl shadow-lg animate-slide-down flex items-center gap-3 border border-red-400">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Statistik Ringkas --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                    <span class="text-3xl">📊</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Statistik Data</h3>
                    <p class="text-sm text-gray-400">Ringkasan data harga emas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Total Data --}}
                <div
                    class="group relative bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-yellow-500 border-opacity-30">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500 opacity-5 rounded-full -mr-16 -mt-16">
                    </div>
                    <div class="relative z-10">
                        <div class="text-yellow-400 text-sm font-bold tracking-wide uppercase mb-2">Total Data</div>
                        <div class="text-4xl font-black text-white mb-2">{{ number_format($hargaEmas->total()) }}</div>
                        <div class="text-xs text-gray-400">Record tersimpan</div>
                    </div>
                </div>

                {{-- Harga Tertinggi --}}
                <div
                    class="group relative bg-gradient-to-br from-yellow-500 to-yellow-600 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-black opacity-10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-yellow-100 text-sm font-bold tracking-wide uppercase mb-2">Harga Tertinggi</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaEmas->max('harga_idr'), 0, ',', '.') }}</div>
                        <div class="text-xs text-yellow-100">All time high</div>
                    </div>
                </div>

                {{-- Harga Terendah --}}
                <div
                    class="group relative bg-gradient-to-br from-gray-700 to-gray-800 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300 border border-gray-600">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-gray-300 text-sm font-bold tracking-wide uppercase mb-2">Harga Terendah</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaEmas->min('harga_idr'), 0, ',', '.') }}</div>
                        <div class="text-xs text-gray-400">All time low</div>
                    </div>
                </div>

                {{-- Harga Rata-rata --}}
                <div
                    class="group relative bg-gradient-to-br from-yellow-600 to-yellow-700 overflow-hidden shadow-xl rounded-xl p-6 transform hover:scale-105 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-black opacity-10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="text-yellow-100 text-sm font-bold tracking-wide uppercase mb-2">Harga Rata-rata</div>
                        <div class="text-4xl font-black text-white mb-2">
                            Rp {{ number_format($hargaEmas->avg('harga_idr'), 0, ',', '.') }}</div>
                        <div class="text-xs text-yellow-100">Average price</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik Harga Historis --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-8 border border-yellow-500 border-opacity-20">
            <div class="flex items-center gap-3 mb-6">
                <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                    <span class="text-3xl">📈</span>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-white">Grafik Harga Historis</h3>
                    <p class="text-sm text-gray-400">Pergerakan harga emas dari waktu ke waktu</p>
                </div>
            </div>
            <div class="relative bg-gray-800 bg-opacity-30 rounded-xl p-4 border border-gray-700">
                <canvas id="chartHistoris" height="70"></canvas>
            </div>
        </div>

        {{-- Filter --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl p-6 border border-yellow-500 border-opacity-20">
            <form action="{{ route('harga-emas.index') }}" method="GET" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-bold text-gray-300 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition-all">
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-bold text-gray-300 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white focus:border-yellow-500 focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50 transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-2 px-6 rounded-lg transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('harga-emas.index') }}"
                        class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded-lg transition-all">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel Data Harga Emas --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl border border-yellow-500 border-opacity-20">
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-yellow-500 bg-opacity-20 rounded-full p-3">
                        <span class="text-3xl">📋</span>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Data Harga Emas Detail</h3>
                        <p class="text-sm text-gray-400">Daftar lengkap harga emas historis</p>
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
                                    Harga USD/oz
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Harga IDR/gram
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Perubahan
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-300 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            @forelse($hargaEmas as $item)
                                <tr class="hover:bg-gray-800 hover:bg-opacity-30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-semibold">
                                        {{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-yellow-400">
                                        ${{ number_format($item->harga_usd, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-200 font-semibold">
                                        Rp {{ number_format($item->harga_idr, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($item->perubahan_persen != null)
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-bold {{ $item->perubahan_persen >= 0 ? 'bg-yellow-500 bg-opacity-20 text-yellow-400' : 'bg-gray-600 bg-opacity-50 text-gray-300' }}">
                                                {{ $item->perubahan_persen >= 0 ? '↑' : '↓' }}
                                                {{ number_format(abs($item->perubahan_persen), 2) }}%
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-xs">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <button onclick="confirmDelete({{ $item->id }})"
                                            class="text-red-400 hover:text-red-300 font-bold transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
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
                                                <p class="text-gray-400 font-semibold mb-2">Tidak ada data</p>
                                                <p class="text-gray-500 text-sm">Silakan tambah data dari Yahoo Finance
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($hargaEmas->hasPages())
                    <div class="mt-6 px-6">
                        {{ $hargaEmas->links() }}
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

        @keyframes modal-show {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-modal-show {
            animation: modal-show 0.3s ease-out;
        }

        .year-option.selected {
            background: linear-gradient(to right, #EAB308, #CA8A04);
            border-color: #EAB308;
            transform: scale(1.05);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Modal Functions
        function openModal() {
            document.getElementById('tambahDataModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            document.getElementById('tambahDataModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('tambahDataModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        // Select years function
        function selectYears(years) {
            // Remove selected class from all buttons
            document.querySelectorAll('.year-option').forEach(btn => {
                btn.classList.remove('selected');
            });

            // Add selected class to clicked button
            event.target.closest('.year-option').classList.add('selected');

            // Set value to input
            document.getElementById('yearsInput').value = years;
        }

        // Form validation
        document.getElementById('tambahDataForm')?.addEventListener('submit', function(e) {
            const years = document.getElementById('yearsInput').value;

            if (!years || years < 1 || years > 20) {
                e.preventDefault();
                Swal.fire({
                    title: 'Input Tidak Valid',
                    text: 'Silakan masukkan jumlah tahun antara 1-20',
                    icon: 'warning',
                    confirmButtonColor: '#EAB308',
                    background: '#1F2937',
                    color: '#F3F4F6'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Sedang Memproses...',
                html: `Mengambil data ${years} tahun dari Yahoo Finance.<br>Mohon tunggu, ini mungkin memakan waktu beberapa menit.`,
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                background: '#1F2937',
                color: '#F3F4F6',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });

        // Fungsi format Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Data grafik dari backend
        const dataGrafik = @json($dataGrafik);

        // Buat chart
        const ctx = document.getElementById('chartHistoris').getContext('2d');

        // Gradient untuk area
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(234, 179, 8, 0.3)');
        gradient.addColorStop(1, 'rgba(234, 179, 8, 0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dataGrafik.map(item => {
                    const date = new Date(item.tanggal);
                    return date.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                }),
                datasets: [{
                    label: 'Harga Emas (IDR/gram)',
                    data: dataGrafik.map(item => item.harga_idr),
                    borderColor: 'rgb(234, 179, 8)',
                    backgroundColor: gradient,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: 'rgb(234, 179, 8)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true
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
                                size: 11,
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
                            maxTicksLimit: 15,
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

        // Fungsi konfirmasi delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Data?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EAB308',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#1F2937',
                color: '#F3F4F6'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request delete
                    fetch(`/harga-emas/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: data.message,
                                    icon: 'success',
                                    confirmButtonColor: '#EAB308',
                                    background: '#1F2937',
                                    color: '#F3F4F6'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: data.message,
                                    icon: 'error',
                                    confirmButtonColor: '#EAB308',
                                    background: '#1F2937',
                                    color: '#F3F4F6'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Terjadi kesalahan saat menghapus data',
                                icon: 'error',
                                confirmButtonColor: '#EAB308',
                                background: '#1F2937',
                                color: '#F3F4F6'
                            });
                        });
                }
            });
        }
    </script>
@endpush
