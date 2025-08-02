<?php

namespace App\Livewire;
use Livewire\Attributes\Title;
use App\Models\Order;
use Livewire\Component;

#[Title('Success | ElectroMart')]

class SuccessPage extends Component
{
    public function render()
    {
        $latest_order = Order::with('address')->where('user_id', auth()->user()->id)->latest()->firstOrFail();
        return view('livewire.success-page', [
            'latest_order' => $latest_order
        ]);
    }
}
