<?php

namespace App\Livewire;

use Livewire\Component;

class ProductCreate extends Component
{
    public $name = '';
    public $amount = '';
    public $qty = '';

    public function save()
    {
        // SECURITY: Validasi lengkap dengan unique constraint
        $validated = $this->validate([
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

        \App\Models\Product::create($validated);

        session()->flash('success', 'Produk berhasil ditambahkan!');

        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.product-create')->layout('layouts.app');
    }
}
