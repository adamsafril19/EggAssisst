<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 pb-20">
    {{-- Header --}}
    <div class="bg-white/80 backdrop-blur px-4 py-3 flex items-center gap-3">
        <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">←</a>
        <h1 class="font-bold text-gray-800">💔 Catat Kerusakan</h1>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mx-4 mt-3 bg-green-100 text-green-700 px-4 py-2 rounded-xl text-center text-sm">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mx-4 mt-3 bg-red-100 text-red-700 px-4 py-2 rounded-xl text-center text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Mode Switch --}}
    <div class="px-4 pt-4">
        <div class="bg-white rounded-xl p-1 flex gap-1">
            <button 
                wire:click="switchMode('butir')"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition-all
                    {{ $mode === 'butir' ? 'bg-amber-500 text-white' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                🥚 Butir
            </button>
            <button 
                wire:click="switchMode('kg')"
                class="flex-1 py-2 rounded-lg text-sm font-medium transition-all
                    {{ $mode === 'kg' ? 'bg-amber-500 text-white' : 'text-gray-500 hover:bg-gray-100' }}"
            >
                ⚖️ Kilogram
            </button>
        </div>
    </div>

    {{-- Form --}}
    <div class="px-4 pt-4">
        <div class="bg-white rounded-3xl shadow-xl p-6">
            <form wire:submit="save" class="space-y-4">
                
                @if($mode === 'butir')
                {{-- Butir Mode: Quick buttons --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Rusak</label>
                    
                    {{-- Display --}}
                    <div class="text-center py-4 bg-gray-50 rounded-xl mb-3">
                        <div class="text-4xl font-bold text-gray-800">{{ $butir }}</div>
                        <div class="text-sm text-gray-500">butir (≈ {{ number_format($kg, 2) }} kg)</div>
                    </div>
                    
                    {{-- Quick Add Buttons --}}
                    <div class="grid grid-cols-4 gap-2">
                        <button 
                            type="button"
                            wire:click="addButir(1)"
                            class="bg-red-100 text-red-600 font-bold py-3 rounded-xl hover:bg-red-200 transition-colors"
                        >
                            +1
                        </button>
                        <button 
                            type="button"
                            wire:click="addButir(3)"
                            class="bg-red-200 text-red-700 font-bold py-3 rounded-xl hover:bg-red-300 transition-colors"
                        >
                            +3
                        </button>
                        <button 
                            type="button"
                            wire:click="addButir(5)"
                            class="bg-red-300 text-red-800 font-bold py-3 rounded-xl hover:bg-red-400 transition-colors"
                        >
                            +5
                        </button>
                        <button 
                            type="button"
                            wire:click="resetButir"
                            class="bg-gray-200 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-300 transition-colors"
                        >
                            ↺
                        </button>
                    </div>
                </div>
                @else
                {{-- KG Mode --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Rusak (kg)</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        wire:model="kg"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all text-lg"
                        placeholder="0"
                    >
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="pecah" class="hidden peer">
                            <div class="peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 bg-gray-50 border-2 border-gray-200 rounded-xl py-3 text-center transition-all">
                                <div class="text-xl">💥</div>
                                <div class="text-xs font-medium mt-1">Pecah</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="retak" class="hidden peer">
                            <div class="peer-checked:bg-orange-500 peer-checked:text-white peer-checked:border-orange-500 bg-gray-50 border-2 border-gray-200 rounded-xl py-3 text-center transition-all">
                                <div class="text-xl">⚡</div>
                                <div class="text-xs font-medium mt-1">Retak</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="type" value="buang" class="hidden peer">
                            <div class="peer-checked:bg-gray-700 peer-checked:text-white peer-checked:border-gray-700 bg-gray-50 border-2 border-gray-200 rounded-xl py-3 text-center transition-all">
                                <div class="text-xl">🗑️</div>
                                <div class="text-xs font-medium mt-1">Buang</div>
                            </div>
                        </label>
                    </div>
                </div>

                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold py-4 rounded-xl shadow-lg hover:shadow-xl transition-all disabled:opacity-50 text-lg"
                >
                    <span wire:loading.remove>Catat 💔</span>
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
            <a href="{{ route('purchase') }}" class="flex flex-col items-center py-2 text-gray-500 hover:text-amber-600">
                <span class="text-xl">📦</span>
                <span class="text-xs mt-1">Beli</span>
            </a>
            <a href="{{ route('damage') }}" class="flex flex-col items-center py-2 text-amber-600">
                <span class="text-xl">💔</span>
                <span class="text-xs mt-1 font-medium">Rusak</span>
            </a>
        </div>
    </div>
</div>
