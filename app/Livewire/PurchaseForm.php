<?php

namespace App\Livewire;

use App\Models\Purchase;
use App\Services\StockService;
use Livewire\Component;

class PurchaseForm extends Component
{
    public string $date = '';
    public float $kg = 0;
    public float $price_per_kg = 0;
    
    public ?float $lastKg = null;
    public ?float $lastPrice = null;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        
        // Auto-fill from last purchase
        $lastPurchase = Purchase::orderBy('date', 'desc')->first();
        if ($lastPurchase) {
            $this->kg = $lastPurchase->kg;
            $this->price_per_kg = $lastPurchase->price_per_kg;
            $this->lastKg = $lastPurchase->kg;
            $this->lastPrice = $lastPurchase->price_per_kg;
        }
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'kg' => 'required|numeric|min:0.01',
            'price_per_kg' => 'required|numeric|min:0',
        ]);

        $product = StockService::getProduct();

        if (!$product) {
            $this->redirectRoute('setup');
            return;
        }

        $result = StockService::recordPurchase(
            $this->date,
            $this->kg,
            $this->price_per_kg
        );

        if ($result['success']) {
            session()->flash('success', $result['message']);
            $this->redirectRoute('dashboard');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render()
    {
        return view('livewire.purchase-form')
            ->layout('components.layouts.app');
    }
}
