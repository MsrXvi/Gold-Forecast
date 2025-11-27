<aside class="w-64 bg-gray-900 text-white flex-shrink-0">
    <!-- Logo -->
    <div class="p-6 flex items-center space-x-3">
        <div
            class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center text-2xl">
            🏅
        </div>
        <div>
            <h1 class="font-bold text-lg">Gold Forecast</h1>
            <p class="text-xs text-gray-400">System</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="mt-8">
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('dashboard') ? 'sidebar-active' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>

        <a href="{{ route('harga-emas.index') }}"
            class="flex items-center px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('harga-emas.*') ? 'sidebar-active' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Data Harga Emas
        </a>

        <a href="{{ route('prediksi.index') }}"
            class="flex items-center px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('prediksi.*') ? 'sidebar-active' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            Prediksi Harga
        </a>

        <a href="{{ route('laporan.index') }}"
            class="flex items-center px-6 py-3 hover:bg-gray-800 transition {{ request()->routeIs('laporan.*') ? 'sidebar-active' : '' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Laporan
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-8">
            @csrf
            <button type="submit" class="flex items-center px-6 py-3 w-full hover:bg-gray-800 transition text-left">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar
            </button>
        </form>
    </nav>
</aside>
