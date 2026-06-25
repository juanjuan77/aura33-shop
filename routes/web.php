<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CrystalAdvisorController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StockAlertController;
use App\Http\Controllers\WholesaleController;
use Illuminate\Support\Facades\Route;

// Tienda
Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/tienda', [ShopController::class, 'shop'])->name('shop');
Route::get('/tienda/{category:slug}', [ShopController::class, 'category'])->name('shop.category');
Route::get('/producto/{product:slug}', [ShopController::class, 'product'])->name('product');
Route::get('/nosotros', [ShopController::class, 'about'])->name('about');

// Carrito
Route::get('/carrito', [CartController::class, 'index'])->name('cart');
Route::post('/carrito/agregar/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/stock-aviso/{product}', [StockAlertController::class, 'store'])->name('stock.alert');
Route::patch('/carrito/actualizar/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/quitar/{product}', [CartController::class, 'remove'])->name('cart.remove');

// Mayoristas - público
Route::get('/mayoristas', [WholesaleController::class, 'info'])->name('wholesale.info');
Route::get('/mayoristas/registro', [WholesaleController::class, 'register'])->name('wholesale.register');
Route::post('/mayoristas/registro', [WholesaleController::class, 'store'])->name('wholesale.store');
Route::get('/mayoristas/gracias', [WholesaleController::class, 'thanks'])->name('wholesale.thanks');
Route::post('/mayoristas/verificar', [WholesaleController::class, 'verify'])->name('wholesale.verify');

// Mayoristas - auth + portal
Route::get('/mayoristas/ingresar', [WholesaleController::class, 'loginForm'])->name('wholesale.login');
Route::post('/mayoristas/ingresar', [WholesaleController::class, 'login'])->name('wholesale.login.post');
Route::post('/mayoristas/salir', [WholesaleController::class, 'logout'])->name('wholesale.logout');
Route::get('/mayoristas/panel', [WholesaleController::class, 'portal'])->name('wholesale.portal');

// Calcular envío
Route::post('/shipping/calculate', [CartController::class, 'calculateShipping'])->name('shipping.calculate');

// Cupones
Route::post('/coupon/apply', [CartController::class, 'applyCoupon'])->name('coupon.apply');

// Asesor de cristales
Route::post('/oraculo', [CrystalAdvisorController::class, 'recommend'])->name('crystal.recommend');

// Checkout
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.place');
Route::post('/pedido/{order}/comprobante', [CartController::class, 'uploadReceipt'])->name('order.receipt');
Route::get('/pedido/{order}/gracias', [CartController::class, 'thanks'])->name('order.thanks');

// MercadoPago callbacks
Route::get('/mp/success', [CartController::class, 'mpSuccess'])->name('mp.success');
Route::get('/mp/failure', [CartController::class, 'mpFailure'])->name('mp.failure');
Route::get('/mp/pending', [CartController::class, 'mpPending'])->name('mp.pending');
Route::post('/mp/webhook', [CartController::class, 'mpWebhook'])->name('mp.webhook');
