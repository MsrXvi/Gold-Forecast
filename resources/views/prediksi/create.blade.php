@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Header Section --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('prediksi.index') }}"
                class="bg-gray-800 hover:bg-gray-700 text-white p-3 rounded-xl transition-all hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-3xl text-gray-900 leading-tight flex items-center gap-3">
                    <span
                        class="bg-gradient-to-r from-yellow-600 via-yellow-500 to-yellow-600 bg-clip-text text-transparent">
                        Buat Prediksi Baru
                    </span>
                </h2>
                <p class="text-sm text-gray-600 mt-2">Konfigurasi dan gunakan Metode Prophet</p>
            </div>
        </div>

        {{-- Alert Messages --}}
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

        {{-- Form Card --}}
        <div
            class="bg-gradient-to-br from-gray-900 to-black overflow-hidden shadow-2xl rounded-2xl border border-yellow-500 border-opacity-20">
            <div class="p-8">

                {{-- Info Header --}}
                <div class="mb-8">
                    <div class="flex items-start gap-4">
                        <div class="bg-yellow-500 bg-opacity-20 rounded-full p-4">
                            <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-2">Prediksi dengan Metode Prophet</h3>
                            <p class="text-gray-400 text-sm leading-relaxed">
                                Metode Prophet akan menganalisis pola historis harga emas dan membuat prediksi akurat untuk
                                periode yang Anda tentukan. Proses ini menggunakan machine learning untuk hasil yang
                                optimal.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Form --}}
                <form action="{{ route('prediksi.generate') }}" method="POST" id="formPrediksi">
                    @csrf

                    {{-- Input Periode --}}
                    <div class="mb-8">
                        <label for="periode_tahun" class="block text-lg font-bold text-white mb-3">
                            📅 Periode Prediksi
                        </label>
                        <div class="relative">
                            <input type="number" name="periode_tahun" id="periode_tahun" min="1" max="10"
                                value="5"
                                class="w-full bg-gray-800 border-2 border-gray-700 text-white text-2xl font-bold rounded-xl px-6 py-4 focus:border-yellow-500 focus:ring-4 focus:ring-yellow-500 focus:ring-opacity-30 transition-all"
                                required>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-500 text-lg font-semibold">
                                Tahun
                            </div>
                        </div>
                        <p class="mt-3 text-sm text-gray-400 flex items-center gap-2">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            Masukkan periode antara 1-10 tahun
                        </p>
                        @error('periode_tahun')
                            <p class="mt-2 text-sm text-red-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-blue-500 bg-opacity-10 border-2 border-blue-500 border-opacity-30 rounded-xl p-6 mb-8">
                        <div class="flex items-start gap-4">
                            <div class="bg-blue-500 bg-opacity-20 rounded-full p-2 flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-300 mb-3 text-lg">ℹ️ Informasi Penting</h4>
                                <ul class="space-y-2 text-sm text-blue-200">
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Proses prediksi membutuhkan waktu <strong>2-5 menit</strong> tergantung
                                            periode</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Pastikan data historis tersedia minimal <strong>200 data</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Model akan menghitung <strong>MAPE, RMSE, MAE, dan R²</strong> secara
                                            otomatis</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span>Hasil prediksi akan otomatis tersimpan ke database</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex gap-4">
                        <a href="{{ route('prediksi.index') }}"
                            class="flex-1 bg-gray-800 hover:bg-gray-700 text-white font-bold py-4 px-6 rounded-xl transition-all text-center border-2 border-gray-700">
                            ← Kembali
                        </a>
                        <button type="submit" id="btnSubmit"
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>Mulai Prediksi</span>
                        </button>
                    </div>
                </form>

                {{-- Loading Indicator --}}
                <div id="loadingIndicator" class="hidden mt-8">
                    <div
                        class="bg-gradient-to-r from-yellow-500 to-yellow-600 bg-opacity-10 border-2 border-yellow-500 border-opacity-30 rounded-xl p-8">
                        <div class="flex flex-col items-center gap-6">
                            {{-- Animated Spinner --}}
                            <div class="relative">
                                <div class="w-20 h-20 border-4 border-gray-700 rounded-full"></div>
                                <div
                                    class="absolute top-0 left-0 w-20 h-20 border-4 border-yellow-500 rounded-full border-t-transparent animate-spin">
                                </div>
                            </div>

                            {{-- Loading Text --}}
                            <div class="text-center">
                                <h4 class="text-2xl font-bold text-white mb-2">Sedang Memproses Prediksi...</h4>
                                <p class="text-gray-400 mb-4">Model Prophet sedang menganalisis data historis</p>
                                <div class="flex items-center justify-center gap-2 text-sm text-gray-500">
                                    <svg class="w-4 h-4 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <span>Estimasi waktu: 2-5 menit</span>
                                </div>
                            </div>

                            {{-- Progress Steps --}}
                            <div class="w-full max-w-md space-y-3">
                                <div class="flex items-center gap-3 text-sm">
                                    <div
                                        class="w-6 h-6 rounded-full bg-yellow-500 flex items-center justify-center text-white font-bold">
                                        ✓</div>
                                    <span class="text-gray-300">Memuat data historis</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div
                                        class="w-6 h-6 rounded-full bg-yellow-500 animate-pulse flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full"></div>
                                    </div>
                                    <span class="text-white font-semibold">Training model Prophet...</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                    </div>
                                    <span class="text-gray-500">Menghitung metrik akurasi</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm">
                                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center">
                                        <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                                    </div>
                                    <span class="text-gray-500">Menyimpan hasil prediksi</span>
                                </div>
                            </div>

                            {{-- Warning --}}
                            <div
                                class="bg-yellow-500 bg-opacity-10 border border-yellow-500 border-opacity-30 rounded-lg px-4 py-3 max-w-md">
                                <p class="text-xs text-yellow-300 text-center">
                                    ⚠️ Mohon jangan tutup atau refresh halaman ini
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

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

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('formPrediksi').addEventListener('submit', function(e) {
            const periode = document.getElementById('periode_tahun').value;

            // Validasi
            if (periode < 1 || periode > 10) {
                e.preventDefault();
                alert('⚠️ Periode harus antara 1-10 tahun');
                return;
            }

            // Konfirmasi
            if (!confirm(
                    `🔮 Mulai prediksi untuk ${periode} tahun ke depan?\n\nProses ini akan memakan waktu beberapa menit.`
                )) {
                e.preventDefault();
                return;
            }

            // Disable button dan tampilkan loading
            const btnSubmit = document.getElementById('btnSubmit');
            btnSubmit.disabled = true;
            btnSubmit.classList.add('opacity-50', 'cursor-not-allowed');

            document.getElementById('loadingIndicator').classList.remove('hidden');

            // Scroll ke loading indicator
            setTimeout(() => {
                document.getElementById('loadingIndicator').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }, 100);
        });
    </script>
@endpush
