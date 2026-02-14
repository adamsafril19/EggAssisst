<?php

namespace App\Services;

use App\Models\Damage;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Get the active product. THE ONLY SOURCE OF TRUTH for product lookup.
     * All components must use this — never Product::first() or hardcoded ID.
     */
    public static function getProduct(): ?Product
    {
        return Product::where('is_active', true)->first();
    }

    /**
     * Get current stock balance from the LEDGER (stock_movements).
     * Single source of truth — reads last movement's balance_after.
     */
    public static function getBalance(?Product $product = null): float
    {
        $product = $product ?? self::getProduct();

        if (!$product) {
            return 0;
        }

        $lastMovement = StockMovement::where('product_id', $product->id)
            ->orderBy('id', 'desc')
            ->first();

        return $lastMovement ? (float) $lastMovement->balance_after : 0;
    }

    /**
     * Get the last balance WITH a pessimistic lock.
     * Must be called inside a transaction.
     */
    private static function getLockedBalance(Product $product): float
    {
        $product = Product::lockForUpdate()->find($product->id);

        $lastMovement = StockMovement::where('product_id', $product->id)
            ->orderBy('id', 'desc')
            ->first();

        return $lastMovement ? (float) $lastMovement->balance_after : (float) ($product->initial_stock_kg ?? 0);
    }

    /**
     * Record a sale with transaction + lock + ledger.
     */
    public static function recordSale(float $kg, float $pricePerKg, ?Product $product = null): array
    {
        $product = $product ?? self::getProduct();

        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }

        return DB::transaction(function () use ($kg, $pricePerKg, $product) {
            $currentBalance = self::getLockedBalance($product);

            if ($currentBalance < $kg) {
                return ['success' => false, 'message' => 'Stok tidak cukup!'];
            }

            $sale = Sale::create([
                'datetime' => now(),
                'kg' => $kg,
                'price_per_kg' => $pricePerKg,
            ]);

            $newBalance = round($currentBalance - $kg, 2);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'kg' => -$kg,
                'balance_after' => $newBalance,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
            ]);

            $product->current_stock_kg = $newBalance;
            $product->save();

            return ['success' => true, 'message' => "+{$kg} kg ✓", 'sale' => $sale];
        });
    }

    /**
     * Record a purchase with transaction + lock + ledger.
     */
    public static function recordPurchase(string $date, float $kg, float $pricePerKg, ?Product $product = null): array
    {
        $product = $product ?? self::getProduct();

        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }

        return DB::transaction(function () use ($date, $kg, $pricePerKg, $product) {
            $currentBalance = self::getLockedBalance($product);

            $purchase = Purchase::create([
                'date' => $date,
                'kg' => $kg,
                'price_per_kg' => $pricePerKg,
            ]);

            $newBalance = round($currentBalance + $kg, 2);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'purchase',
                'kg' => $kg,
                'balance_after' => $newBalance,
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
            ]);

            $product->current_stock_kg = $newBalance;
            $product->save();

            return ['success' => true, 'message' => "+{$kg} kg ✓", 'purchase' => $purchase];
        });
    }

    /**
     * Record damage with transaction + lock + ledger.
     */
    public static function recordDamage(float $kg, string $type, ?Product $product = null): array
    {
        $product = $product ?? self::getProduct();

        if (!$product) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }

        return DB::transaction(function () use ($kg, $type, $product) {
            $currentBalance = self::getLockedBalance($product);

            if ($currentBalance < $kg) {
                return ['success' => false, 'message' => 'Jumlah kerusakan melebihi stok!'];
            }

            $damage = Damage::create([
                'date' => now()->format('Y-m-d'),
                'kg' => $kg,
                'type' => $type,
            ]);

            $newBalance = round($currentBalance - $kg, 2);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'damage',
                'kg' => -$kg,
                'balance_after' => $newBalance,
                'reference_type' => Damage::class,
                'reference_id' => $damage->id,
            ]);

            $product->current_stock_kg = $newBalance;
            $product->save();

            return ['success' => true, 'message' => "Kerusakan tercatat.", 'damage' => $damage];
        });
    }

    /**
     * Set initial stock (on first setup) with ledger entry.
     */
    public static function setInitialStock(float $kg, ?Product $product = null): void
    {
        $product = $product ?? self::getProduct();
        if (!$product) return;

        DB::transaction(function () use ($kg, $product) {
            $product = Product::lockForUpdate()->find($product->id);
            $product->initial_stock_kg = $kg;
            $product->current_stock_kg = $kg;
            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'initial',
                'kg' => $kg,
                'balance_after' => $kg,
                'note' => 'Stok awal',
            ]);
        });
    }

    /**
     * Get the configurable average egg weight from product settings.
     */
    public static function getAvgEggWeight(?Product $product = null): float
    {
        $product = $product ?? self::getProduct();
        return $product?->avg_egg_weight ?? 0.06;
    }
}
