<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

/**
 * ProductCreate Component
 * 
 * Handles the creation of new products with form validation including
 * unique name constraint and numeric validation for price and quantity.
 * 
 * @package App\Livewire
 */
class ProductCreate extends Component
{
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
     * Validate and save the new product to the database.
     * 
     * This method:
     * 1. Validates all input fields (including unique name constraint)
     * 2. Creates the product in database
     * 3. Flashes success message
     * 4. Redirects to product index
     * 
     * Validation Rules:
     * - name: required, string, max 255 chars, unique in products table
     * - amount: required, numeric, minimum 0
     * - qty: required, integer, minimum 0
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function save()
    {
        // SECURITY: Comprehensive validation with unique constraint
        $validatedData = $this->validate([
            'name' => 'required|string|max:255|unique:products,name',
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

        // Create product using mass assignment (protected by $fillable in Model)
        Product::create($validatedData);

        // Flash success message (displayed via toast in layout)
        session()->flash('success', 'Produk berhasil ditambahkan!');

        return redirect()->route('products.index');
    }

    /**
     * Render the product creation form.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.product-create')->layout('layouts.app');
    }
}

