<?php

namespace App\Console\Commands;

use App\Models\DailySummary;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\StockService;
use Illuminate\Console\Command;

class GenerateDailySummary extends Command
{
    protected $signature = 'summary:generate {--date= : Date to generate summary for (default: today)}';
    protected $description = 'Generate daily summary for sales and stock';

    public function handle(): int
    {
        $date = $this->option('date') 
            ? \Carbon\Carbon::parse($this->option('date')) 
            : now();
        
        $dateString = $date->format('Y-m-d');
        
        $this->info("Generating summary for {$dateString}...");

        $product = StockService::getProduct();
        
        if (!$product) {
            $this->error('No product found. Please set up the product first.');
            return 1;
        }

        // Calculate totals
        $todaySales = Sale::whereDate('datetime', $dateString)->get();
        $totalSold = $todaySales->sum('kg');
        $totalRevenue = $todaySales->sum(fn($sale) => $sale->kg * $sale->price_per_kg);
        
        // Get average purchase cost
        $todayPurchase = Purchase::whereDate('date', $dateString)->first();
        $avgCost = $todayPurchase ? $todayPurchase->price_per_kg : 0;
        
        // Calculate profit estimate
        $profitEst = $totalRevenue - ($totalSold * $avgCost);

        // Create or update summary
        DailySummary::updateOrCreate(
            ['date' => $dateString],
            [
                'total_sold' => $totalSold,
                'profit_est' => max(0, $profitEst),
                'closing_stock' => $product->current_stock_kg,
            ]
        );

        $this->info("Summary generated:");
        $this->table(
            ['Metric', 'Value'],
            [
                ['Date', $dateString],
                ['Total Sold', "{$totalSold} kg"],
                ['Revenue', "Rp " . number_format($totalRevenue, 0, ',', '.')],
                ['Est. Profit', "Rp " . number_format($profitEst, 0, ',', '.')],
                ['Closing Stock', "{$product->current_stock_kg} kg"],
            ]
        );

        return 0;
    }
}
