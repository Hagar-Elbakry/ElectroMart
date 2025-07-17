<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products | ElectroMart')] 


class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $featured;

    #[Url]
    public $on_sale;

    #[Url]
    public $price_range = 300000;

    #[Url]
    public $sort = 'latest';
    public function render()
    {
        $products = Product::where('is_active', 1);
        $categories = Category::where('is_active', 1)->get(['id', 'name', 'slug']);
        $brands = Brand::where('is_active', 1)->get(['id', 'name', 'slug']); 

        if(!empty($this->selected_categories)) {
            $products->whereIn('category_id', $this->selected_categories);
        }

        if(!empty($this->selected_brands)) {
            $products->whereIn('brand_id', $this->selected_brands);
        }

        if($this->featured) {
            $products->where('is_featured', 1);
        }

        if($this->on_sale) {
            $products->where('on_sale', 1);
        }

        if($this->price_range) {
            $products->whereBetween('price', [0, $this->price_range]);
        }

        if($this->sort == 'latest') {
            $products->latest();
        } else {
            $products->orderBy('price');
        }

        return view('livewire.products-page', [
            'products' => $products->paginate(6),
            'categories' => $categories,
            'brands' => $brands
        ]);
    }
}
