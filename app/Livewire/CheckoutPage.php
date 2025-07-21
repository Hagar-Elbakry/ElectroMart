<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Mail\OrderPlaced;
use App\Models\Address;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

#[Title('Checkout')]

class CheckoutPage extends Component
{

    public $first_name;
    public $last_name;
    public $phone;
    public $street_address;
    public $city;
    public $state;
    public $zip_code;
    public $payment_method;

    public function mount() {
        $cart_items = CartManagement::getCartItemsFromCookie();

        if(count($cart_items) == 0) {
            return redirect('/products');
        }
    }

    public function placeOrder() {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required',
            'street_address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required'
        ]);

        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        $order = Order::create([
            'grand_total' => $grand_total,
            'payment_method' => $this->payment_method,
            'payment_status' => 'pending',
            'status' => 'new',
            'currency' => 'EGP',
            'shipping_amount' => 0,
            'shipping_method' => 'none',
            'notes' => 'Order placed by ' . auth()->user()->name,
            'user_id' => auth()->user()->id
        ]);

        Address::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'street_address' => $this->street_address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'order_id' => $order->id
        ]);

        foreach($cart_items as $item) {
            $order->items()->create([
                'quantity' => $item['quantity'],
                'unit_amount' => $item['unit_amount'],
                'total_amount' => $item['total_amount'],
                'product_id' => $item['product_id']
            ]);
        }
        
        CartManagement::clearCartItemsFromCookie();
        Mail::to(auth()->user())->send(new OrderPlaced($order));
        return redirect('/success');
    }

    public function render()
    {
        $cart_items = CartManagement::getCartItemsFromCookie();
        $grand_total = CartManagement::calculateGrandTotal($cart_items);

        return view('livewire.checkout-page', [
            'cart_items' => $cart_items,
            'grand_total' => $grand_total
        ]);
    }
}
