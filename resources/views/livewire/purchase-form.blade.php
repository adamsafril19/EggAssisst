<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 pb-20">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur px-4 py-3 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">←</a>
        <h1 class="font-bold text-gray-800">📦 Tambah Stok</h1>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mx-4 mt-3 bg-green-100 text-green-700 px-4 py-2 rounded-xl text-center text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Auto-fill hint --}}
    @if($lastKg)
    <div class="mx-4 mt-3 bg-blue-50 text-blue-600 px-4 py-2 rounded-xl text-center text-sm">
        💡 Auto-fill dari pembelian terakhir
    </div>
    @endif

    {{-- Form --}}
    <div class="px-4 pt-4">
        <div class="bg-white rounded-3xl shadow-xl p-6">
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input 
                        type="date" 
                        wire:model="date"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                        required
                    >
                    @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (kg)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        wire:model="kg"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 transition-all text-lg"
                        placeholder="0"
                        required
                    >
                    @error('kg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp/kg)</label>
                    <input 
                        type="number" 
                        step="100" 
                        wire:model="price_per_kg"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                        placeholder="0"
                        required
                    >
                    @error('price_per_kg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-emerald-500 to-green-500 text-white font-semibold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 text-lg"
                >
                    <span wire:loading.remove>Tambah Stok 📦</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </form>
        </div>
    </div>

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
            <a href="{{ route('purchase') }}" class="flex flex-col items-center py-2 text-amber-600">
                <span class="text-xl">📦</span>
                <span class="text-xs mt-1 font-medium">Beli</span>
            </a>
            <a href="{{ route('damage') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">💔</span>
                <span class="text-xs mt-1">Rusak</span>
            </a>
        </div>
    </div>
</div>
