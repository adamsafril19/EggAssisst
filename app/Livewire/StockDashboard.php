<?php

namespace App\Livewire;

use App\Services\StockAnalyticsService;
use App\Services\StockService;
use Livewire\Component;

class StockDashboard extends Component
{
    public function quickSale($kg)
    {
        $kg = floatval($kg);

        if ($kg <= 0) {
            session()->flash('error', 'Jumlah penjualan tidak valid.');
            return;
        }

        $product = StockService::getProduct();

        if (!$product) {
            $this->redirectRoute('setup');
            return;
        }

        if ($product->current_price <= 0) {
            session()->flash('error', 'Harga belum diset. Atur di Pengaturan.');
            return;
        }

        $result = StockService::recordSale($kg, $product->current_price, $product);

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render(StockAnalyticsService $analytics)
    {
        $product = StockService::getProduct();

        if (!$product) {
            $this->redirectRoute('setup');
            return view('livewire.stock-dashboard', [
                'product' => null,
                'daysRemaining' => null,
                'stockStatus' => ['status' => 'unknown', 'color' => 'gray', 'label' => '', 'emoji' => ''],
                'todaySold' => 0,
                'todayRevenue' => 0,
                'lastSale' => null,
                'reorderInfo' => null,
            ])->layout('components.layouts.app');
        }

        $todaySummary = $analytics->getTodaySummary();

        return view('livewire.stock-dashboard', [
            'product' => $product,
            'daysRemaining' => $analytics->getDaysRemaining($product),
            'stockStatus' => $analytics->getStockStatus($product),
            'todaySold' => $todaySummary['total_sold'],
            'todayRevenue' => $todaySummary['total_revenue'],
            'lastSale' => $analytics->getLastSale(),
            'reorderInfo' => $analytics->getReorderInfo($product),
        ])->layout('components.layouts.app');
    }
}
