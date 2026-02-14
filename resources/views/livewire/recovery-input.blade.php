<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 pb-20">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur px-4 py-3 flex items-center gap-3">
        <a href="{{ route('settings') }}" class="text-gray-500 hover:text-gray-700">←</a>
        <h1 class="font-bold text-gray-800">🔧 Koreksi Data</h1>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mx-4 mt-3 bg-green-100 text-green-700 px-4 py-2 rounded-xl text-center text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Warning --}}
    <div class="mx-4 mt-4 bg-amber-50 border border-amber-200 rounded-xl p-4">
        <p class="text-amber-700 text-sm">
            ⚠️ Fitur ini akan <strong>menghapus</strong> semua data penjualan hari ini dan menggantinya dengan nilai baru.
        </p>
        <p class="text-amber-600 text-xs mt-2">
            📋 Semua perubahan tercatat di audit log.
        </p>
    </div>

    {{-- Current Data --}}
    <div class="px-4 pt-4">
        <div class="bg-white/60 rounded-xl p-4">
            <p class="text-gray-500 text-sm">Data hari ini saat ini:</p>
            <p class="text-xl font-bold text-gray-800">{{ number_format($todaysSales, 2) }} kg</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="px-4 pt-4">
        <div class="bg-white rounded-3xl shadow-xl p-6">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Total Penjualan Hari Ini (kg)
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        wire:model="total_kg_sold"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all text-lg"
                        placeholder="0"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Alasan (opsional)
                    </label>
                    <input 
                        type="text" 
                        wire:model="reason"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-400 focus:ring-2 focus:ring-gray-200 transition-all text-sm"
                        placeholder="Misal: Lupa catat pagi"
                    >
                </div>

                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50"
                    onclick="return confirm('Yakin mau mengganti data hari ini?')"
                >
                    <span wire:loading.remove>Perbarui Data</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Recent Corrections (Audit Log) --}}
    @if($recentCorrections && $recentCorrections->count() > 0)
    <div class="px-4 pt-4">
        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Riwayat Koreksi</p>
        <div class="bg-white/60 rounded-xl divide-y divide-gray-100">
            @foreach($recentCorrections as $log)
            <div class="p-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ $log->date->format('d M') }}</span>
                    <span class="text-gray-700">{{ number_format($log->old_value, 2) }} → {{ number_format($log->new_value, 2) }} kg</span>
                </div>
                @if($log->reason)
                <p class="text-xs text-gray-400 mt-1">{{ $log->reason }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Bottom Navigation --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 px-4 py-3">
        <div class="grid grid-cols-4 gap-2 max-w-md mx-auto">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">🏠</span>
                <span class="text-xs mt-1">Beranda</span>
            </a>
            <a href="{{ route('report') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">📋</span>
                <span class="text-xs mt-1">Laporan</span>
            </a>
            <a href="{{ route('purchase') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">📦</span>
                <span class="text-xs mt-1">Beli</span>
            </a>
            <a href="{{ route('damage') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">💔</span>
                <span class="text-xs mt-1">Rusak</span>
            </a>
        </div>
    </div>
</div>
