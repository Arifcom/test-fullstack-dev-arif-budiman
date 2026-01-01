<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProductRequest
 * 
 * Form Request for validating product update data.
 * 
 * NOTE: Currently not used in the application as validation is handled
 * directly in the ProductEdit Livewire component. This class is kept
 * for potential future use with traditional controllers.
 * 
 * If used, should include unique validation with exception for current product:
 * 'name' => 'required|string|max:255|unique:products,name,' . $productId
 * 
 * @package App\Http\Requests
 */
class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 
     * Currently returns true for all users. In production, you may want to
     * add permission checks here (e.g., user roles, policies).
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 
     * Basic validation rules without unique constraint.
     * If using this FormRequest, consider adding unique validation
     * with exception for the current product ID.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'qty' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom validation error messages in Indonesian.
     * 
     * Provides user-friendly error messages for each validation rule
     * to improve user experience and clarity.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk wajib diisi.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'amount.required' => 'Harga produk wajib diisi.',
            'amount.numeric' => 'Harga produk harus berupa angka.',
            'amount.min' => 'Harga produk tidak boleh kurang dari 0.',
            'qty.required' => 'Jumlah stok wajib diisi.',
            'qty.integer' => 'Jumlah stok harus berupa angka bulat.',
            'qty.min' => 'Jumlah stok tidak boleh kurang dari 0.',
        ];
    }
}

