<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * ProductIndex Component
 * 
 * Handles the display of product list with search functionality,
 * pagination, and delete confirmation modal.
 * 
 * @package App\Livewire
 */
class ProductIndex extends Component
{
    use WithPagination;

    /**
     * Search query string for filtering products by name.
     *
     * @var string
     */
    public $search = '';

    /**
     * Controls the visibility of the delete confirmation modal.
     *
     * @var bool
     */
    public $confirmingProductDeletion = false;

    /**
     * Stores the ID of the product to be deleted.
     *
     * @var int|null
     */
    public $productToDeleteId = null;

    /**
     * Open the delete confirmation modal for a specific product.
     * 
     * This method sets the product ID and shows the confirmation modal
     * without immediately deleting the product.
     *
     * @param int $productId The ID of the product to delete
     * @return void
     */
    public function confirmDelete($productId)
    {
        $this->confirmingProductDeletion = true;
        $this->productToDeleteId = $productId;
    }

    /**
     * Close the delete confirmation modal without deleting.
     * 
     * Resets the modal state and clears the selected product ID.
     *
     * @return void
     */
    public function cancelDelete()
    {
        $this->confirmingProductDeletion = false;
        $this->productToDeleteId = null;
    }

    /**
     * Delete the confirmed product from the database.
     * 
     * This method:
     * 1. Deletes the product from database
     * 2. Closes the confirmation modal
     * 3. Dispatches a success toast notification
     *
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete()
    {
        $product = Product::findOrFail($this->productToDeleteId);
        $product->delete();

        // Reset modal state
        $this->confirmingProductDeletion = false;
        $this->productToDeleteId = null;

        // Dispatch toast notification to Alpine.js
        $this->dispatch('notify', message: 'Produk berhasil dihapus!', type: 'success');
    }

    /**
     * Render the product index view with paginated, searchable products.
     * 
     * Products are:
     * - Filtered by search query (if provided)
     * - Ordered by creation date (newest first)
     * - Paginated (10 per page)
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $paginatedProducts = Product::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.product-index', [
            'products' => $paginatedProducts,
        ])->layout('layouts.app');
    }
}

