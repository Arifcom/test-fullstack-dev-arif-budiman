<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * SECURITY NOTES:
     * ---------------
     * 1. 'name' - required|string|max:255|unique:products,name
     *    - required: Mencegah NULL values yang dapat menyebabkan bugs dan inkonsistensi data
     *    - string: Type safety - mencegah injection attacks dengan memastikan hanya string yang diterima
     *    - max:255: Mencegah buffer overflow attacks dan DoS melalui input yang sangat panjang
     *    - unique: Mencegah duplikasi data, menjaga integritas inventory, dan menghindari konflik bisnis logic
     *
     * 2. 'amount' - required|numeric|min:0
     *    - required: Memastikan setiap produk memiliki harga (business rule)
     *    - numeric: Mencegah type confusion attacks dan memastikan kalkulasi matematis yang akurat
     *    - min:0: Business logic protection - harga tidak boleh negatif, mencegah manipulasi data finansial
     *
     * 3. 'qty' - required|integer|min:0
     *    - required: Inventory management - setiap produk harus punya data stok
     *    - integer: Type safety - stok harus berupa bilangan bulat, tidak ada 0.5 unit produk
     *    - min:0: Mencegah stok negatif yang tidak masuk akal dalam konteks bisnis
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // SECURITY: Unique constraint mencegah duplikasi produk dan konflik inventory
            'name' => 'required|string|max:255|unique:products,name',
            
            // SECURITY: Numeric validation mencegah type confusion dan manipulasi data finansial
            'amount' => 'required|numeric|min:0',
            
            // SECURITY: Integer validation memastikan stok selalu bilangan bulat yang valid
            'qty' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'name.unique' => 'Nama produk sudah digunakan. Silakan gunakan nama yang berbeda.',
            'amount.required' => 'Harga produk wajib diisi.',
            'amount.numeric' => 'Harga produk harus berupa angka.',
            'amount.min' => 'Harga produk tidak boleh kurang dari 0.',
            'qty.required' => 'Jumlah stok wajib diisi.',
            'qty.integer' => 'Jumlah stok harus berupa angka bulat.',
            'qty.min' => 'Jumlah stok tidak boleh kurang dari 0.',
        ];
    }
}
