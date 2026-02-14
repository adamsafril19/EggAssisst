<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\StockService;
use Livewire\Component;

class SetupForm extends Component
{
    public float $current_stock_kg = 0;
    public float $current_price = 0;
    public float $alert_threshold_days = 2;
    public int $lead_time_days = 1;
    public float $avg_egg_weight = 0.06;
    public bool $isEditing = false;

    public function mount()
    {
        $product = StockService::getProduct();
        
        if ($product) {
            $this->current_stock_kg = $product->current_stock_kg;
            $this->current_price = $product->current_price;
            $this->alert_threshold_days = $product->alert_threshold_days;
            $this->lead_time_days = $product->lead_time_days ?? 1;
            $this->avg_egg_weight = $product->avg_egg_weight ?? 0.06;
            $this->isEditing = true;
        }
    }

    public function save()
    {
        $this->validate([
            'current_stock_kg' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'alert_threshold_days' => 'required|numeric|min:1',
            'lead_time_days' => 'required|integer|min:1',
            'avg_egg_weight' => 'required|numeric|min:0.01|max:1',
        ]);

        $isNew = !$this->isEditing;

        $product = StockService::getProduct();
        
        if ($product) {
            $product->update([
                'current_price' => $this->current_price,
                'alert_threshold_days' => $this->alert_threshold_days,
                'lead_time_days' => $this->lead_time_days,
                'avg_egg_weight' => $this->avg_egg_weight,
            ]);
        } else {
            $product = Product::create([
                'name' => 'Telur',
                'is_active' => true,
                'current_price' => $this->current_price,
                'alert_threshold_days' => $this->alert_threshold_days,
                'lead_time_days' => $this->lead_time_days,
                'avg_egg_weight' => $this->avg_egg_weight,
            ]);
        }

        // For new setup: set initial stock via StockService (with audit trail)
        if ($isNew) {
            StockService::setInitialStock($this->current_stock_kg, $product);
        } else {
            // For editing: just sync the current_stock_kg cache
            $product->current_stock_kg = $this->current_stock_kg;
            $product->save();
        }

        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        return view('livewire.setup-form')
            ->layout('components.layouts.app');
    }
}
