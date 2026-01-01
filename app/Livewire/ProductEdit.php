<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

/**
 * ProductEdit Component
 * 
 * Handles the editing of existing products with form validation.
 * Includes unique name validation that excludes the current product
 * to allow users to update without changing the name.
 * 
 * @package App\Livewire
 */
class ProductEdit extends Component
{
    /**
     * The ID of the product being edited.
     *
     * @var int
     */
    public $productId;

    /**
     * Product name input.
     *
     * @var string
     */
    public $name = '';

    /**
     * Product price/amount input.
     *
     * @var string|float
     */
    public $amount = '';

    /**
     * Product quantity/stock input.
     *
     * @var string|int
     */
    public $qty = '';

    /**
     * Initialize component with existing product data.
     * 
     * Loads the product from database and populates form fields.
     *
     * @param int $id The product ID from route parameter
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function mount($id)
    {
        $product = Product::findOrFail($id);
        
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->amount = $product->amount;
        $this->qty = $product->qty;
    }

    /**
     * Validate and update the product in the database.
     * 
     * This method:
     * 1. Validates all input fields (unique name excludes current product)
     * 2. Updates the product in database
     * 3. Flashes success message
     * 4. Redirects to product index
     * 
     * Validation Rules:
     * - name: required, string, max 255 chars, unique except current product
     * - amount: required, numeric, minimum 0
     * - qty: required, integer, minimum 0
     * 
     * SECURITY NOTE: The unique validation includes exception for current product ID
     * to allow users to update other fields without triggering "name already exists" error.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        // SECURITY: Unique validation with exception for current product
        // Allows user to update product without "name already taken" error from the record itself
        $validatedData = $this->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $this->productId,
            'amount' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'name.unique' => 'Nama produk sudah digunakan. Silakan gunakan nama yang berbeda.',
            'amount.required' => 'Harga produk wajib diisi.',
            'amount.numeric' => 'Harga produk harus berupa angka.',
            'amount.min' => 'Harga produk tidak boleh kurang dari 0.',
            'qty.required' => 'Jumlah stok wajib diisi.',
            'qty.integer' => 'Jumlah stok harus berupa angka bulat.',
            'qty.min' => 'Jumlah stok tidak boleh kurang dari 0.',
        ]);

        $product = Product::findOrFail($this->productId);
        $product->update($validatedData);

        // Flash success message (displayed via toast in layout)
        session()->flash('success', 'Produk berhasil diperbarui!');

        return redirect()->route('products.index');
    }

    /**
     * Render the product edit form.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.product-edit')->layout('layouts.app');
    }
}

