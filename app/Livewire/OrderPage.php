<?php

namespace App\Livewire;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Detail | ElectroMart')]

class OrderPage extends Component
{
    public $order_id;

    public function mount($order_id) {
        $this->order_id = $order_id;
        $order = Order::where('id', $this->order_id)->firstOrFail();
        if($order->user_id != auth()->user()->id) {
            abort(403);
        }
    }


    public function render()
    {
        $order_items = OrderItem::with('product')->where('order_id', $this->order_id)->get();
        $order = Order::where('id', $this->order_id)->first();
        $address = Address::where('order_id', $this->order_id)->first();
        return view('livewire.order-page', [
            'order_items' => $order_items,
            'order' => $order,
            'address' => $address
        ]);
    }
}
