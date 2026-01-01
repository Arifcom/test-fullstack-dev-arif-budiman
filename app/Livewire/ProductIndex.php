<?php

namespace App\Livewire;

use Livewire\Component;

class ProductIndex extends Component
{
    public $search = '';

    public $confirmingProductDeletion = false;
    public $productToDeleteId = null;

    use \Livewire\WithPagination;

    public function confirmDelete($id)
    {
        $this->confirmingProductDeletion = true;
        $this->productToDeleteId = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingProductDeletion = false;
        $this->productToDeleteId = null;
    }

    public function delete()
    {
        $product = \App\Models\Product::findOrFail($this->productToDeleteId);
        $product->delete();

        $this->confirmingProductDeletion = false;
        $this->productToDeleteId = null;

        $this->dispatch('notify', message: 'Produk berhasil dihapus!', type: 'success');
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
