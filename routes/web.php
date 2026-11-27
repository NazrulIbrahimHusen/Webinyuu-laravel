<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Pub\CartController;
use App\Http\Controllers\Pub\CatalogController;
use App\Http\Controllers\Pub\CheckoutController;
use App\Http\Controllers\Pub\OrderTrackController;
use App\Http\Controllers\Seller;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'))->name('home');

require __DIR__.'/auth.php';

// Profil akun (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ---------- Seller ----------
Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('onboarding', [Seller\OnboardingController::class, 'create'])->name('seller.onboarding');
    Route::post('onboarding', [Seller\OnboardingController::class, 'store'])->name('seller.onboarding.store');

    Route::get('dashboard', Seller\DashboardController::class)->name('dashboard');

    Route::prefix('dashboard')->name('seller.')->group(function () {
        Route::get('toko', [Seller\StoreProfileController::class, 'edit'])->name('store.edit');
        Route::put('toko', [Seller\StoreProfileController::class, 'update'])->name('store.update');

        Route::resource('categories', Seller\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('products', Seller\ProductController::class)->except(['show']);

        // Langganan: bisa diakses semua paket, termasuk Basic
        Route::get('langganan', [Seller\SubscriptionController::class, 'index'])->name('subscription.index');
        Route::post('langganan/invoice', [Seller\SubscriptionController::class, 'store'])->name('subscription.store');
        Route::get('langganan/invoice/{id}', [Seller\SubscriptionController::class, 'show'])->name('subscription.show');
        Route::post('langganan/invoice/{id}/bukti', [Seller\SubscriptionController::class, 'uploadProof'])->name('subscription.proof');
        Route::get('langganan/invoice/{id}/bukti', [Seller\SubscriptionController::class, 'proof'])->name('subscription.proof.view');
        Route::post('langganan/invoice/{id}/batal', [Seller\SubscriptionController::class, 'cancel'])->name('subscription.cancel');

        Route::middleware('feature:landing_page')->group(function () {
                Route::get('landing', [Seller\LandingController::class, 'edit'])->name('landing.edit');
                Route::put('landing', [Seller\LandingController::class, 'update'])->name('landing.update');
                Route::get('landing/pratinjau', [Seller\LandingController::class, 'preview'])->name('landing.preview');
                Route::post('landing/testimoni', [Seller\LandingController::class, 'storeTestimonial'])->name('landing.testimonials.store');
                Route::delete('landing/testimoni/{id}', [Seller\LandingController::class, 'destroyTestimonial'])->name('landing.testimonials.destroy');
                Route::post('landing/faq', [Seller\LandingController::class, 'storeFaq'])->name('landing.faqs.store');
                Route::delete('landing/faq/{id}', [Seller\LandingController::class, 'destroyFaq'])->name('landing.faqs.destroy');
            });

        // Fitur terima pesanan: hanya Starter/Pro
        Route::middleware('feature:orders')->group(function () {
            Route::resource('payment-methods', Seller\PaymentMethodController::class)->only(['index', 'store', 'update', 'destroy']);

            Route::get('pengiriman', [Seller\ShippingController::class, 'index'])->name('shipping.index');
            Route::post('pengiriman/pengaturan', [Seller\ShippingController::class, 'saveSettings'])->name('shipping.settings');
            Route::post('pengiriman/zona', [Seller\ShippingController::class, 'storeZone'])->name('shipping.zones.store');
            Route::put('pengiriman/zona/{id}', [Seller\ShippingController::class, 'updateZone'])->name('shipping.zones.update');
            Route::delete('pengiriman/zona/{id}', [Seller\ShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');

            Route::get('pesanan', [Seller\OrderController::class, 'index'])->name('orders.index');
            Route::get('pesanan/{id}', [Seller\OrderController::class, 'show'])->name('orders.show');
            Route::post('pesanan/{id}/status', [Seller\OrderController::class, 'updateStatus'])->name('orders.status');
            Route::post('pesanan/{id}/resi', [Seller\OrderController::class, 'saveShipment'])->name('orders.shipment');
            Route::get('pesanan/{id}/bukti/{paymentId}', [Seller\OrderController::class, 'proof'])->name('orders.proof');
            Route::post('pesanan/{id}/bukti/{paymentId}/verifikasi', [Seller\OrderController::class, 'verifyPayment'])->name('orders.verify');
            Route::post('pesanan/{id}/bukti/{paymentId}/tolak', [Seller\OrderController::class, 'rejectPayment'])->name('orders.reject');
        });
    });
});

// ---------- Admin ----------
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::get('pembayaran', [Admin\SubscriptionPaymentController::class, 'index'])->name('payments.index');
    Route::get('pembayaran/{id}', [Admin\SubscriptionPaymentController::class, 'show'])->name('payments.show');
    Route::get('pembayaran/{id}/bukti', [Admin\SubscriptionPaymentController::class, 'proof'])->name('payments.proof');
    Route::post('pembayaran/{id}/verifikasi', [Admin\SubscriptionPaymentController::class, 'verify'])->name('payments.verify');
    Route::post('pembayaran/{id}/tolak', [Admin\SubscriptionPaymentController::class, 'reject'])->name('payments.reject');

    Route::get('seller', [Admin\SellerController::class, 'index'])->name('sellers.index');
    Route::post('seller/{id}/langganan', [Admin\SellerController::class, 'grant'])->name('sellers.grant');
    Route::post('seller/{id}/toggle', [Admin\SellerController::class, 'toggleStore'])->name('sellers.toggle');

    Route::get('paket', [Admin\PlanController::class, 'index'])->name('plans.index');
    Route::put('paket/{id}', [Admin\PlanController::class, 'update'])->name('plans.update');

    Route::resource('rekening', Admin\PlatformAccountController::class)->only(['index', 'store', 'update', 'destroy'])
        ->parameters(['rekening' => 'id'])->names('accounts');
});

// ---------- Pembeli: pesanan via token, tanpa akun ----------
Route::prefix('pesanan/{token}')->group(function () {
    Route::get('/', [OrderTrackController::class, 'show'])->name('order.show');
    Route::post('bukti-bayar', [OrderTrackController::class, 'uploadProof'])->name('order.proof');
    Route::post('diterima', [OrderTrackController::class, 'received'])->name('order.received');
});

// ---------- Publik: HARUS PALING BAWAH ----------
Route::prefix('{store}')
    ->where(['store' => '[a-z0-9]+(?:-[a-z0-9]+)*'])
    ->middleware('store.active')
    ->group(function () {
        Route::get('/', [CatalogController::class, 'home'])->name('store.home'); // landing page atau katalog
        Route::get('katalog', [CatalogController::class, 'show'])->name('store.catalog');
        Route::get('produk/{productSlug}', [CatalogController::class, 'product'])->name('store.product');

        Route::middleware('feature:checkout')->group(function () {
            Route::get('keranjang', [CartController::class, 'show'])->name('store.cart');
            Route::post('keranjang', [CartController::class, 'add'])->name('store.cart.add');
            Route::patch('keranjang', [CartController::class, 'update'])->name('store.cart.update');
            Route::delete('keranjang/{product}', [CartController::class, 'remove'])->name('store.cart.remove');
            Route::get('checkout', [CheckoutController::class, 'show'])->name('store.checkout');
            Route::post('checkout', [CheckoutController::class, 'place'])->name('store.checkout.place');
        });
    });
