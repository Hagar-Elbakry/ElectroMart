<?php

use App\Livewire\Auth\ForgetPage;
use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPage;
use App\Livewire\CancelPage;
use App\Livewire\CartPage;
use App\Livewire\CategoriesPage;
use App\Livewire\CheckoutPage;
use App\Livewire\HomePage;
use App\Livewire\OrderPage;
use App\Livewire\OrdersPage;
use App\Livewire\ProductPage;
use App\Livewire\ProductsPage;
use App\Livewire\SuccessPage;
use Illuminate\Support\Facades\Route;
use League\Csv\Query\Row;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomePage::class);
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/products/{slug}', ProductPage::class);
Route::get('/cart', CartPage::class);


Route::middleware('guest')->group(function() {
    Route::get('/login', LoginPage::class)->name('login');
    Route::get('/register', RegisterPage::class);
    Route::get('/forget-password', ForgetPage::class)->name('password.request');
    Route::get('/reset-password/{token}', ResetPage::class)->name('password.reset');
});

Route::middleware('auth')->group(function() {
    Route::get('/logout', function() {
        auth()->logout();
        return redirect('/');
    });
    Route::get('/checkout', CheckoutPage::class);
    Route::get('/orders', OrdersPage::class);
    Route::get('/orders/{order}', OrderPage::class)->name('orders.show');
    Route::get('/success', SuccessPage::class);
    Route::get('/cancel', CancelPage::class);
});

