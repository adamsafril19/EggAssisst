<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-100 flex items-center justify-center p-4">
    <div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl p-6 w-full max-w-md">
        @if($isEditing)
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 mb-4 text-sm">
            ← Kembali
        </a>
        @endif
        
        <div class="text-center mb-6">
            <div class="text-5xl mb-3">🥚</div>
            <h1 class="text-xl font-bold text-gray-800">EggAssisst</h1>
            <p class="text-gray-500 text-sm">{{ $isEditing ? 'Pengaturan' : 'Setup Awal' }}</p>
        </div>

        <form wire:submit="save" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ $isEditing ? 'Stok Saat Ini' : 'Stok Awal' }} (kg)
                </label>
                <input 
                    type="number" 
                    step="0.01" 
                    wire:model="current_stock_kg"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                    placeholder="0"
                    required
                >
                @error('current_stock_kg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Harga Jual (Rp/kg)
                </label>
                <input 
                    type="number" 
                    step="100" 
                    wire:model="current_price"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                    placeholder="0"
                    required
                >
                @error('current_price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alert (hari)
                </label>
                <input 
                    type="number" 
                    step="0.5" 
                    wire:model="alert_threshold_days"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                    placeholder="2"
                    required
                >
                <p class="text-xs text-gray-400 mt-1">Notifikasi saat stok ≤ X hari</p>
                @error('alert_threshold_days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Lead Time Supplier (hari)
                </label>
                <input 
                    type="number" 
                    step="1" 
                    wire:model="lead_time_days"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                    placeholder="1"
                    required
                    min="1"
                >
                <p class="text-xs text-gray-400 mt-1">Waktu tunggu pengiriman dari supplier</p>
                @error('lead_time_days') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Berat Rata-rata Telur (kg)
                </label>
                <input 
                    type="number" 
                    step="0.001" 
                    wire:model="avg_egg_weight"
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all"
                    placeholder="0.06"
                    required
                    min="0.01"
                    max="1"
                >
                <p class="text-xs text-gray-400 mt-1">Default: 0.06 kg (60 gram). Untuk konversi butir → kg.</p>
                @error('avg_egg_weight') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white font-semibold py-3 rounded-xl shadow-lg hover:shadow-xl transition-all"
            >
                {{ $isEditing ? 'Simpan' : 'Mulai 🚀' }}
            </button>
        </form>

        {{-- Secondary Actions (hidden features) --}}
        @if($isEditing)
        <div class="mt-6 pt-4 border-t border-gray-200 space-y-2">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-3">Fitur Lainnya</p>
            
            <a href="{{ route('damage') }}" class="flex items-center justify-between text-gray-500 hover:text-gray-700 text-sm py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors">
                <span>💔 Catat Kerusakan</span>
                <span>→</span>
            </a>
            
            <a href="{{ route('recovery') }}" class="flex items-center justify-between text-gray-500 hover:text-gray-700 text-sm py-2 px-3 rounded-lg hover:bg-gray-50 transition-colors">
                <span>🔧 Koreksi Data Hari Ini</span>
                <span>→</span>
            </a>
        </div>
        @endif
    </div>
</div>
