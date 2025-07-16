<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products | ElectroMart')] 
class ProductsPage extends Component
{
    use WithPagination;
    public function render()
    {
        $products = Product::where('is_active', 1);
        $categories = Category::where('is_active', 1)->get(['id', 'name', 'slug']);
        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']); 
        return view('livewire.products-page', [
            'products' => $products->paginate(6),
            'categories' => $categories,
            'brands' => $brands
        ]);
    }
}
