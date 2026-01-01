<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * StoreProductRequest
 * 
 * Form Request for validating product creation data.
 * 
 * This class handles validation for new product submissions including:
 * - Unique name constraint
 * - Numeric price validation with minimum value
 * - Integer quantity validation with minimum value
 * - Custom Indonesian error messages
 * 
 * @package App\Http\Requests
 */
class StoreProductRequest extends FormRequest
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
     * SECURITY NOTES:
     * ---------------
     * 1. 'name' - required|string|max:255|unique:products,name
     *    - required: Prevents NULL values that can cause bugs and data inconsistency
     *    - string: Type safety - prevents injection attacks by ensuring only strings are accepted
     *    - max:255: Prevents buffer overflow attacks and DoS through extremely long input
     *    - unique: Prevents data duplication, maintains inventory integrity, avoids business logic conflicts
     *
     * 2. 'amount' - required|numeric|min:0
     *    - required: Ensures every product has a price (business rule)
     *    - numeric: Prevents type confusion attacks and ensures accurate mathematical calculations
     *    - min:0: Business logic protection - price cannot be negative, prevents financial data manipulation
     *
     * 3. 'qty' - required|integer|min:0
     *    - required: Inventory management - every product must have stock data
     *    - integer: Type safety - stock must be whole numbers, no 0.5 unit products
     *    - min:0: Prevents negative stock which doesn't make sense in business context
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // SECURITY: Unique constraint prevents product duplication and inventory conflicts
            'name' => 'required|string|max:255|unique:products,name',
            
            // SECURITY: Numeric validation prevents type confusion and financial data manipulation
            'amount' => 'required|numeric|min:0',
            
            // SECURITY: Integer validation ensures stock is always a valid whole number
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

