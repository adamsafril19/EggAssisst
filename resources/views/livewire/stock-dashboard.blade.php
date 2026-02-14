<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 pb-20" wire:poll.10s>
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xl">🥚</span>
            <span class="font-bold text-gray-800">EggAssisst</span>
        </div>
        <a href="{{ route('settings') }}" class="text-gray-400 hover:text-gray-600 text-xl">⚙️</a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mx-4 mt-3 bg-green-100 text-green-700 px-4 py-2 rounded-xl text-center text-sm font-medium">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mx-4 mt-3 bg-red-100 text-red-700 px-4 py-2 rounded-xl text-center text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Main Stock Display with STATUS --}}
    <div class="px-4 pt-6 pb-4">
        <div class="bg-white rounded-3xl shadow-xl p-6 text-center">
            <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Stok</p>
            <div class="text-5xl font-black text-gray-800">
                {{ number_format($product->current_stock_kg, 2) }}
                <span class="text-xl font-medium text-gray-400">kg</span>
            </div>
            
            {{-- STATUS INDICATOR --}}
            <div class="mt-3 inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium
                @if($stockStatus['color'] === 'green') bg-green-100 text-green-700
                @elseif($stockStatus['color'] === 'yellow') bg-amber-100 text-amber-700
                @elseif($stockStatus['color'] === 'red') bg-red-100 text-red-700
                @else bg-gray-100 text-gray-500
                @endif
            ">
                <span>{{ $stockStatus['emoji'] }}</span>
                <span>{{ $stockStatus['label'] }}</span>
                @if($daysRemaining)
                <span class="opacity-75">(~{{ round($daysRemaining) }} hari)</span>
                @endif
            </div>
        </div>
    </div>

    {{-- TODAY'S PROGRESS (promoted, with target bar) --}}
    <div class="px-4 pb-4">
        <div class="bg-white rounded-2xl shadow-lg p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Jual Hari Ini</p>
            
            <div class="flex justify-between items-end mb-2">
                <div>
                    <span class="text-2xl font-bold text-gray-800">{{ number_format($todaySold, 2) }}</span>
                    <span class="text-gray-500">kg</span>
                </div>
                <div class="text-right">
                    <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                </div>
            </div>


        </div>
    </div>

    {{-- QUICK SALE with CONTEXT LABEL --}}
    <div class="px-4 pb-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3 text-center">🛒 Jual Cepat</p>
        <div class="grid grid-cols-3 gap-3">
            <button 
                wire:click="quickSale(0.25)"
                wire:loading.attr="disabled"
                class="bg-gradient-to-br from-emerald-400 to-emerald-600 text-white font-bold py-5 rounded-2xl shadow-lg active:scale-95 transition-transform text-lg disabled:opacity-50"
            >
                ¼ kg
            </button>
            <button 
                wire:click="quickSale(0.5)"
                wire:loading.attr="disabled"
                class="bg-gradient-to-br from-blue-400 to-blue-600 text-white font-bold py-5 rounded-2xl shadow-lg active:scale-95 transition-transform text-lg disabled:opacity-50"
            >
                ½ kg
            </button>
            <button 
                wire:click="quickSale(1)"
                wire:loading.attr="disabled"
                class="bg-gradient-to-br from-purple-400 to-purple-600 text-white font-bold py-5 rounded-2xl shadow-lg active:scale-95 transition-transform text-lg disabled:opacity-50"
            >
                1 kg
            </button>
        </div>
        <div wire:loading class="text-center mt-2 text-gray-400 text-sm">⏳</div>
    </div>

    {{-- LAST ACTIVITY + REORDER COUNTDOWN --}}
    <div class="px-4 pb-4">
        <div class="bg-white/60 rounded-xl p-3 space-y-2">
            @if($lastSale)
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500">Terakhir jual:</span>
                <span class="text-gray-700 font-medium">{{ $lastSale['kg'] }} kg · {{ $lastSale['time_ago'] }}</span>
            </div>
            @endif

            {{-- COUNTDOWN REORDER --}}
            @if($reorderInfo)
            <div class="flex items-center justify-between text-sm
                @if($reorderInfo['urgency_color'] === 'red') text-red-600 font-medium
                @elseif($reorderInfo['urgency_color'] === 'amber') text-amber-600
                @else text-gray-600
                @endif
            ">
                <span class="flex items-center gap-1">
                    <span>⏰</span>
                    @if($reorderInfo['urgency'] === 'sekarang')
                    <span>Order sekarang!</span>
                    @else
                    <span>Order sebelum:</span>
                    @endif
                </span>
                <span class="font-medium">
                    @if($reorderInfo['urgency'] === 'sekarang')
                    Stok bisa habis!
                    @else
                    {{ $reorderInfo['reorder_date_formatted'] }}
                    @endif
                </span>
            </div>
            <div class="text-xs text-gray-400">
                Lead time supplier: {{ $reorderInfo['lead_time'] }} hari
            </div>
            @endif

            @if($stockStatus['status'] === 'bahaya')
            <div class="flex items-center gap-2 text-red-600 text-sm font-medium">
                <span>⚠️</span>
                <span>Stok akan habis {{ number_format($daysRemaining, 0) }} hari lagi!</span>
            </div>
            @elseif($stockStatus['status'] === 'waspada')
            <div class="flex items-center gap-2 text-amber-600 text-sm">
                <span>📦</span>
                <span>Pertimbangkan untuk beli stok</span>
            </div>
            @endif
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-4 py-3 safe-area-bottom">
        <div class="grid grid-cols-4 gap-2 max-w-md mx-auto">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 text-amber-600">
                <span class="text-xl">🏠</span>
                <span class="text-xs mt-1 font-medium">Beranda</span>
            </a>
            <a href="{{ route('report') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600 transition-colors">
                <span class="text-xl">📋</span>
                <span class="text-xs mt-1">Laporan</span>
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
