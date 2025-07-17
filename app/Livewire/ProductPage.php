<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product Detail | ElectroMart')]

class ProductPage extends Component
{
    public $slug;

    public $quantity = 1;

    public function increaseQty() {
        $this->quantity++;
    }

    public function addToCart($product_id) {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);
         
        LivewireAlert::title('Product Added To Cart Successfully!')
        ->position('bottom-end')
        ->success()
        ->timer(3000)
        ->toast()
        ->show();
    }

    public function decreaseQty() {
        if($this->quantity > 1)
        $this->quantity--;
    }

    public function mount($slug) {
        $this->slug = $slug;
    }

    public function render()
    {
        $product = Product::where('slug', $this->slug)->firstOrFail();
        return view('livewire.product-page', [
            'product' => $product
        ]);
    }
}
