<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 pb-20">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur px-4 py-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 text-lg">←</a>
        <span class="font-bold text-gray-800">📋 Laporan Mingguan</span>
        <div class="w-6"></div>
    </div>

    {{-- Week Selector --}}
    <div class="px-4 pt-4 pb-2">
        <div class="flex items-center justify-between bg-white rounded-2xl shadow px-4 py-3">
            <button wire:click="previousWeek" class="text-gray-400 hover:text-gray-700 text-xl font-bold px-2">‹</button>
            <div class="text-center">
                <p class="text-sm font-semibold text-gray-800">{{ $periodLabel }}</p>
                @if($isCurrentWeek)
                <p class="text-xs text-amber-600 font-medium">Minggu ini</p>
                @endif
            </div>
            <button
                wire:click="nextWeek"
                @if($isCurrentWeek) disabled @endif
                class="text-xl font-bold px-2 {{ $isCurrentWeek ? 'text-gray-200' : 'text-gray-400 hover:text-gray-700' }}"
            >›</button>
        </div>
    </div>

    {{-- Main Report Card --}}
    <div class="px-4 py-2">
        <div class="bg-white rounded-3xl shadow-xl p-6 space-y-5">

            {{-- Penjualan --}}
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Penjualan</p>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-3xl font-black text-gray-800">{{ number_format($report['total_sold_kg'], 2) }}</span>
                        <span class="text-gray-400">kg</span>
                    </div>
                    <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($report['total_revenue'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- Pembelian --}}
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Pembelian</p>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-2xl font-bold text-gray-700">{{ number_format($report['total_purchased_kg'], 2) }}</span>
                        <span class="text-gray-400">kg</span>
                    </div>
                    <span class="text-base font-semibold text-red-500">Rp {{ number_format($report['total_purchase_cost'], 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- Kerusakan --}}
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Kerusakan</p>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-2xl font-bold text-gray-700">{{ number_format($report['total_damaged_kg'], 2) }}</span>
                        <span class="text-gray-400">kg</span>
                    </div>
                    @if($report['total_damaged_kg'] > 0)
                    <span class="text-sm text-red-400">💔</span>
                    @else
                    <span class="text-sm text-green-500">✓ Tidak ada</span>
                    @endif
                </div>
            </div>

            <div class="border-t-2 border-gray-200"></div>

            {{-- Estimasi Laba --}}
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Estimasi Laba</p>
                @if($report['avg_cost_per_kg'] > 0)
                <div class="text-3xl font-black {{ $report['estimated_profit'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    Rp {{ number_format($report['estimated_profit'], 0, ',', '.') }}
                </div>
                <p class="text-xs text-gray-400 mt-1">Pendapatan − (kg terjual × rata² harga beli)</p>
                @else
                <p class="text-sm text-gray-400">Belum ada data pembelian minggu ini</p>
                @endif
            </div>

        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="px-4 py-2 grid grid-cols-2 gap-3">
        {{-- Rata-rata jual/hari --}}
        <div class="bg-white rounded-2xl shadow p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Rata² Jual/Hari</p>
            <p class="text-2xl font-bold text-gray-800">{{ $report['avg_sold_per_day'] }}</p>
            <p class="text-xs text-gray-400">kg/hari</p>
        </div>

        {{-- Stok Akhir --}}
        <div class="bg-white rounded-2xl shadow p-4 text-center">
            <p class="text-xs text-gray-400 mb-1">Stok {{ $isCurrentWeek ? 'Sekarang' : 'Akhir' }}</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($report['closing_stock'], 2) }}</p>
            <p class="text-xs text-gray-400">kg</p>
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-4 py-3 safe-area-bottom">
        <div class="grid grid-cols-4 gap-2 max-w-md mx-auto">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600 transition-colors">
                <span class="text-xl">🏠</span>
                <span class="text-xs mt-1">Beranda</span>
            </a>
            <a href="{{ route('report') }}" class="flex flex-col items-center py-2 text-amber-600">
                <span class="text-xl">📋</span>
                <span class="text-xs mt-1 font-medium">Laporan</span>
            </a>
            <a href="{{ route('purchase') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600 transition-colors">
                <span class="text-xl">📦</span>
                <span class="text-xs mt-1">Beli</span>
            </a>
            <a href="{{ route('damage') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600 transition-colors">
                <span class="text-xl">💔</span>
                <span class="text-xs mt-1">Rusak</span>
            </a>
        </div>
    </div>
</div>
