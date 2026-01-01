<?php

namespace App\Livewire;

use Livewire\Component;

class ProductEdit extends Component
{
    public $productId;
    public $name = '';
    public $amount = '';
    public $qty = '';

    public function mount($id)
    {
        $product = \App\Models\Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->amount = $product->amount;
        $this->qty = $product->qty;
    }

    public function update()
    {
        // SECURITY: Unique validation dengan exception untuk produk saat ini
        // Memungkinkan user update produk tanpa error "nama sudah digunakan" dari record itu sendiri
        $validated = $this->validate([
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

        $product = \App\Models\Product::findOrFail($this->productId);
        $product->update($validated);

        session()->flash('success', 'Produk berhasil diperbarui!');

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.product-edit')->layout('layouts.app');
    }
}
