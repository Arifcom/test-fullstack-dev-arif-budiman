<?php

namespace App\Livewire;

use Livewire\Component;

class ProductIndex extends Component
{
    public $search = '';

    public function delete($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $product->delete();

        session()->flash('success', 'Produk berhasil dihapus!');
    }

    public function render()
    {
        $products = \App\Models\Product::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.product-index', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
