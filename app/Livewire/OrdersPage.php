<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Orders | ElectroMart')]

class OrdersPage extends Component
{
    public function render()
    {
        $orders = Order::where('user_id', auth()->user()->id)->latest()->paginate(5);
        return view('livewire.orders-page', [
            'orders' => $orders
        ]);
    }
}
