<?php

namespace App\Livewire;

use App\Services\StockService;
use Livewire\Component;

class DamageForm extends Component
{
    public float $kg = 0;
    public int $butir = 0;
    public string $type = 'pecah';
    public string $mode = 'butir';
    
    private function getEggWeight(): float
    {
        return StockService::getAvgEggWeight();
    }

    public function addButir(int $count)
    {
        $this->butir += $count;
        $this->kg = round($this->butir * $this->getEggWeight(), 2);
    }

    public function resetButir()
    {
        $this->butir = 0;
        $this->kg = 0;
    }

    public function switchMode(string $mode)
    {
        $this->mode = $mode;
        $this->butir = 0;
        $this->kg = 0;
    }

    public function save()
    {
        $eggWeight = $this->getEggWeight();
        $actualKg = $this->mode === 'butir' 
            ? round($this->butir * $eggWeight, 2)
            : $this->kg;

        if ($actualKg <= 0) {
            session()->flash('error', 'Masukkan jumlah kerusakan!');
            return;
        }

        $product = StockService::getProduct();

        if (!$product) {
            $this->redirectRoute('setup');
            return;
        }

        $result = StockService::recordDamage($actualKg, $this->type, $product);

        if ($result['success']) {
            $label = $this->mode === 'butir' ? "{$this->butir} butir" : "{$actualKg} kg";
            session()->flash('success', "Kerusakan {$label} tercatat.");
            $this->redirectRoute('dashboard');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function render()
    {
        $product = StockService::getProduct();
        $eggWeight = StockService::getAvgEggWeight($product);

        return view('livewire.damage-form', [
            'eggWeight' => $eggWeight,
        ])->layout('components.layouts.app');
    }
}
