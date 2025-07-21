<?php

namespace App\Livewire;


use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Order Detail | ElectroMart')]

class OrderPage extends Component
{
    public function render()
    {
        
        return view('livewire.order-page');
    }
}
