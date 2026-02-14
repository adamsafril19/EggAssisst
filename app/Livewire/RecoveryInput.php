<?php

namespace App\Livewire;

use App\Models\CorrectionLog;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class RecoveryInput extends Component
{
    public float $total_kg_sold = 0;
    public string $reason = '';

    public function save()
    {
        $this->validate([
            'total_kg_sold' => 'required|numeric|min:0',
        ]);

        $product = StockService::getProduct();
        
        if (!$product) {
            $this->redirectRoute('setup');
            return;
        }

        $today = now()->format('Y-m-d');
        $oldValue = Sale::whereDate('datetime', $today)->sum('kg');

        DB::transaction(function () use ($product, $today, $oldValue) {
            $product = Product::lockForUpdate()->find($product->id);

            CorrectionLog::create([
                'date' => $today,
                'old_value' => $oldValue,
                'new_value' => $this->total_kg_sold,
                'reason' => $this->reason ?: 'Koreksi manual',
            ]);

            $todaySaleIds = Sale::whereDate('datetime', $today)->pluck('id');
            StockMovement::where('reference_type', Sale::class)
                ->whereIn('reference_id', $todaySaleIds)
                ->delete();
            Sale::whereDate('datetime', $today)->delete();

            $currentBalance = StockService::getBalance($product);

            if ($this->total_kg_sold > 0) {
                $remaining = $this->total_kg_sold;
                $runningBalance = $currentBalance;
                
                while ($remaining > 0) {
                    if ($remaining >= 1) {
                        $amount = [0.25, 0.5, 1][array_rand([0.25, 0.5, 1])];
                    } elseif ($remaining >= 0.5) {
                        $amount = [0.25, 0.5][array_rand([0.25, 0.5])];
                    } else {
                        $amount = min(0.25, $remaining);
                    }
                    
                    $amount = min($amount, $remaining);

                    $sale = Sale::create([
                        'datetime' => now()->setTime(rand(6, 20), rand(0, 59), rand(0, 59)),
                        'kg' => $amount,
                        'price_per_kg' => $product->current_price,
                    ]);

                    $runningBalance = round($runningBalance - $amount, 2);

                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'sale',
                        'kg' => -$amount,
                        'balance_after' => $runningBalance,
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                    ]);

                    $remaining -= $amount;
                    $remaining = round($remaining, 2);
                }

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'correction',
                    'kg' => $oldValue - $this->total_kg_sold,
                    'balance_after' => $runningBalance,
                    'note' => "Koreksi: {$oldValue} → {$this->total_kg_sold} kg. " . ($this->reason ?: ''),
                ]);

                $product->current_stock_kg = $runningBalance;
            } else {
                $product->current_stock_kg = $currentBalance;
            }
            
            $product->save();
        });

        session()->flash('success', "Data diperbarui: {$oldValue} kg → {$this->total_kg_sold} kg");
        
        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        $product = StockService::getProduct();
        $today = now()->format('Y-m-d');
        $todaysSales = Sale::whereDate('datetime', $today)->sum('kg');
        
        $recentCorrections = CorrectionLog::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.recovery-input', [
            'product' => $product,
            'todaysSales' => $todaysSales,
            'recentCorrections' => $recentCorrections,
        ])->layout('components.layouts.app');
    }
}
