<?php

namespace App\Livewire;

use App\Models\Damage;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WeeklyReport extends Component
{
    public string $weekStart;
    public string $weekEnd;

    public function mount(?string $week = null)
    {
        // Default: current week (Mon-Sun)
        if ($week) {
            $start = Carbon::parse($week)->startOfWeek();
        } else {
            $start = now()->startOfWeek();
        }

        $this->weekStart = $start->format('Y-m-d');
        $this->weekEnd = $start->copy()->endOfWeek()->format('Y-m-d');
    }

    public function previousWeek()
    {
        $start = Carbon::parse($this->weekStart)->subWeek();
        $this->weekStart = $start->format('Y-m-d');
        $this->weekEnd = $start->copy()->endOfWeek()->format('Y-m-d');
    }

    public function nextWeek()
    {
        $next = Carbon::parse($this->weekStart)->addWeek();

        // Don't go past current week
        if ($next->gt(now()->endOfWeek())) {
            return;
        }

        $this->weekStart = $next->format('Y-m-d');
        $this->weekEnd = $next->copy()->endOfWeek()->format('Y-m-d');
    }

    public function isCurrentWeek(): bool
    {
        return Carbon::parse($this->weekStart)->isSameWeek(now());
    }

    protected function getReportData(): array
    {
        $start = $this->weekStart;
        $end = $this->weekEnd;

        // Sales: single aggregate query
        $sales = DB::table('sales')
            ->selectRaw('COALESCE(SUM(kg), 0) as total_kg, COALESCE(SUM(kg * price_per_kg), 0) as revenue')
            ->whereDate('datetime', '>=', $start)
            ->whereDate('datetime', '<=', $end)
            ->first();

        // Purchases: single aggregate query
        $purchases = DB::table('purchases')
            ->selectRaw('COALESCE(SUM(kg), 0) as total_kg, COALESCE(SUM(kg * price_per_kg), 0) as total_cost')
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->first();

        // Damages: single aggregate query
        $damages = DB::table('damages')
            ->selectRaw('COALESCE(SUM(kg), 0) as total_kg')
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->first();

        $totalSoldKg = (float) $sales->total_kg;
        $totalRevenue = (float) $sales->revenue;
        $totalPurchasedKg = (float) $purchases->total_kg;
        $totalPurchaseCost = (float) $purchases->total_cost;
        $totalDamagedKg = (float) $damages->total_kg;

        // Avg cost per kg purchased this week (for profit estimate)
        $avgCostPerKg = $totalPurchasedKg > 0
            ? $totalPurchaseCost / $totalPurchasedKg
            : 0;

        // Estimasi laba: pendapatan - (kg terjual × rata-rata harga beli)
        $estimatedProfit = $avgCostPerKg > 0
            ? $totalRevenue - ($totalSoldKg * $avgCostPerKg)
            : 0;

        // Days in range (for avg/day)
        $daysInRange = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
        $avgSoldPerDay = $daysInRange > 0 ? $totalSoldKg / $daysInRange : 0;

        // Closing stock
        $product = StockService::getProduct();
        $closingStock = $product ? (float) $product->current_stock_kg : 0;

        return [
            'total_sold_kg' => $totalSoldKg,
            'total_revenue' => $totalRevenue,
            'total_purchased_kg' => $totalPurchasedKg,
            'total_purchase_cost' => $totalPurchaseCost,
            'total_damaged_kg' => $totalDamagedKg,
            'estimated_profit' => $estimatedProfit,
            'avg_sold_per_day' => round($avgSoldPerDay, 2),
            'closing_stock' => $closingStock,
            'avg_cost_per_kg' => $avgCostPerKg,
        ];
    }

    public function render()
    {
        $report = $this->getReportData();

        $periodLabel = Carbon::parse($this->weekStart)->translatedFormat('j M')
            . ' – '
            . Carbon::parse($this->weekEnd)->translatedFormat('j M Y');

        return view('livewire.weekly-report', [
            'report' => $report,
            'periodLabel' => $periodLabel,
            'isCurrentWeek' => $this->isCurrentWeek(),
        ])->layout('components.layouts.app');
    }
}
