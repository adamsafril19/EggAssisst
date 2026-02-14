<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class StockAnalyticsService
{
    /**
     * Average kg sold per day over last 14 days.
     */
    public function getBurnRate(): float
    {
        $total = Sale::where('datetime', '>=', now()->subDays(7))->sum('kg');
        return $total / 7;
    }

    /**
     * Estimated days until stock runs out.
     */
    public function getDaysRemaining(?Product $product = null): ?float
    {
        $product = $product ?? StockService::getProduct();
        if (!$product) return null;

        $burnRate = $this->getBurnRate();
        if ($burnRate <= 0) return null;

        return $product->current_stock_kg / $burnRate;
    }

    /**
     * Stock status: bahaya / waspada / aman.
     */
    public function getStockStatus(?Product $product = null): array
    {
        $product = $product ?? StockService::getProduct();
        $daysRemaining = $this->getDaysRemaining($product);

        if ($daysRemaining === null) {
            return ['status' => 'unknown', 'color' => 'gray', 'label' => 'Belum ada data', 'emoji' => '⚪'];
        }

        $threshold = $product->alert_threshold_days ?? 2;

        if ($daysRemaining <= $threshold) {
            return ['status' => 'bahaya', 'color' => 'red', 'label' => 'Hampir Habis', 'emoji' => '🔴'];
        }

        if ($daysRemaining <= $threshold * 3) {
            return ['status' => 'waspada', 'color' => 'yellow', 'label' => 'Waspada', 'emoji' => '🟠'];
        }

        return ['status' => 'aman', 'color' => 'green', 'label' => 'Aman', 'emoji' => '🟢'];
    }

    /**
     * Today's total sold (kg) and revenue (Rp).
     */
    public function getTodaySummary(): array
    {
        $today = now()->format('Y-m-d');

        $row = DB::table('sales')
            ->selectRaw('COALESCE(SUM(kg), 0) as total_sold, COALESCE(SUM(kg * price_per_kg), 0) as total_revenue')
            ->whereDate('datetime', $today)
            ->first();

        return [
            'total_sold' => (float) $row->total_sold,
            'total_revenue' => (float) $row->total_revenue,
        ];
    }

    /**
     * Last sale info.
     */
    public function getLastSale(): ?array
    {
        $lastSale = Sale::orderBy('datetime', 'desc')->first();
        if (!$lastSale) return null;

        $minutes = now()->diffInMinutes($lastSale->datetime);

        if ($minutes < 1) $timeAgo = 'Baru saja';
        elseif ($minutes < 60) $timeAgo = "{$minutes} menit lalu";
        else $timeAgo = now()->diffInHours($lastSale->datetime) . ' jam lalu';

        return ['kg' => $lastSale->kg, 'time_ago' => $timeAgo];
    }

    /**
     * Reorder alert: should user order now?
     */
    public function getReorderInfo(?Product $product = null): ?array
    {
        $product = $product ?? StockService::getProduct();
        if (!$product) return null;

        $daysRemaining = $this->getDaysRemaining($product);
        if ($daysRemaining === null) return null;

        $leadTime = $product->lead_time_days ?? 1;
        $daysUntilReorder = max(0, floor($daysRemaining) - $leadTime);
        $reorderDate = now()->addDays($daysUntilReorder);

        if ($daysUntilReorder <= 0) {
            $urgency = 'sekarang';
            $urgencyColor = 'red';
        } elseif ($daysUntilReorder <= 2) {
            $urgency = 'segera';
            $urgencyColor = 'amber';
        } else {
            $urgency = 'normal';
            $urgencyColor = 'gray';
        }

        return [
            'days_until_reorder' => $daysUntilReorder,
            'reorder_date_formatted' => $reorderDate->translatedFormat('l, j M'),
            'lead_time' => $leadTime,
            'urgency' => $urgency,
            'urgency_color' => $urgencyColor,
        ];
    }
}
