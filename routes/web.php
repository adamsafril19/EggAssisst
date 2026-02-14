<?php

use App\Livewire\DamageForm;
use App\Livewire\PurchaseForm;
use App\Livewire\RecoveryInput;
use App\Livewire\SetupForm;
use App\Livewire\StockDashboard;
use App\Livewire\WeeklyReport;
use App\Services\StockService;
use Illuminate\Support\Facades\Route;

// Redirect logic based on product existence
Route::get('/', function () {
    $product = StockService::getProduct();
    
    if (!$product) {
        return redirect()->route('setup');
    }
    
    return redirect()->route('dashboard');
});

// Setup page (first time)
Route::get('/setup', SetupForm::class)->name('setup');

// Main dashboard (includes recap, 80% of activity)
Route::get('/dashboard', StockDashboard::class)->name('dashboard');

// Settings (edit product config)
Route::get('/settings', SetupForm::class)->name('settings');

// Purchase input
Route::get('/purchase', PurchaseForm::class)->name('purchase');

// Damage input
Route::get('/damage', DamageForm::class)->name('damage');

// Recovery input (hidden, accessed via settings)
Route::get('/recovery', RecoveryInput::class)->name('recovery');

// Weekly report
Route::get('/report', WeeklyReport::class)->name('report');
