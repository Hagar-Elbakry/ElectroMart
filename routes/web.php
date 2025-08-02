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
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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

Route::get('/', HomePage::class)->name('home');
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/products/{slug}', ProductPage::class);
Route::get('/cart', CartPage::class);


Route::middleware('guest')->group(function() {
    Route::get('/login', LoginPage::class)->middleware('throttle:6,1')->name('login');
    Route::get('/register', RegisterPage::class);
    Route::get('/forget-password', ForgetPage::class)->middleware('throttle:6,1')->name('password.request');
    Route::get('/reset-password/{token}', ResetPage::class)->middleware('throttle:6,1')->name('password.reset');
});

Route::middleware('auth')->group(function() {
    Route::get('/email/verify', function () {
        return view('livewire.auth.verify-email');
    })->middleware('throttle:6,1')->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect('/');
    })->middleware('signed')->name('verification.verify');


    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('/logout', function() {
        auth()->logout();
        return redirect('/');
    })->name('logout');
    Route::get('/checkout', CheckoutPage::class)->middleware('verified');
    Route::get('/orders', OrdersPage::class)->middleware('verified');
    Route::get('/orders/{order_id}', OrderPage::class)->middleware('verified')->name('orders.show');
    Route::get('/success', SuccessPage::class)->middleware('verified');
});

