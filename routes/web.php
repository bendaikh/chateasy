<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

// WhatsApp Webhook (no auth required for external services)
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');

// Dashboard - redirects to main dashboard
Route::get('/dashboard', function () {
    return redirect()->route('app.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Main App Routes (for all authenticated users)
Route::middleware(['auth'])->prefix('app')->name('app.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/whatsapp', [CustomerDashboardController::class, 'whatsapp'])->name('whatsapp');
    Route::get('/ai-settings', [CustomerDashboardController::class, 'aiSettings'])->name('ai-settings');
    Route::get('/conversations', [CustomerDashboardController::class, 'conversations'])->name('conversations');
    Route::get('/conversations/{id}', [CustomerDashboardController::class, 'conversationDetail'])->name('conversation.detail');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/products', [CustomerDashboardController::class, 'products'])->name('products');
    Route::get('/products/create', [CustomerDashboardController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [CustomerDashboardController::class, 'productsStore'])->name('products.store');
    Route::get('/campaigns', [CustomerDashboardController::class, 'campaigns'])->name('campaigns');
    
    // Categories
    Route::get('/categories', [CustomerDashboardController::class, 'categories'])->name('categories');
    Route::post('/categories', [CustomerDashboardController::class, 'categoriesStore'])->name('categories.store');
    Route::put('/categories/{id}', [CustomerDashboardController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [CustomerDashboardController::class, 'categoriesDestroy'])->name('categories.destroy');
    
    // Website Customization
    Route::get('/website-customization', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'index'])->name('website-customization');
    Route::post('/website-customization', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'update'])->name('website-customization.update');
    Route::get('/website-preview', [\App\Http\Controllers\Admin\WebsiteCustomizationController::class, 'preview'])->name('website-preview');
    
    // WhatsApp Routes
    Route::post('/whatsapp/generate-qr', [WhatsAppController::class, 'generateQrCode'])->name('whatsapp.generate-qr');
    Route::get('/whatsapp/check-connection', [WhatsAppController::class, 'checkConnection'])->name('whatsapp.check-connection');
    Route::post('/whatsapp/save-connection', [WhatsAppController::class, 'saveConnection'])->name('whatsapp.save-connection');
    Route::post('/whatsapp/disconnect/{profile}', [WhatsAppController::class, 'disconnect'])->name('whatsapp.disconnect');
    Route::get('/whatsapp/{profile}/conversations', [WhatsAppController::class, 'getConversations'])->name('whatsapp.conversations');
    Route::get('/whatsapp/conversations/{conversation}/messages', [WhatsAppController::class, 'getMessages'])->name('whatsapp.messages');
    Route::post('/whatsapp/conversations/{conversation}/send', [WhatsAppController::class, 'sendMessage'])->name('whatsapp.send');
});

require __DIR__.'/auth.php';
