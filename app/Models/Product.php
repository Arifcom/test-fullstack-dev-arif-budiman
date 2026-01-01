<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Product Model
 * 
 * Represents a product in the inventory system.
 * 
 * Database Table: products
 * 
 * Attributes:
 * @property int $id Primary key
 * @property string $name Product name (max 255 chars, unique)
 * @property float $amount Product price/amount (decimal with 2 precision)
 * @property int $qty Product quantity/stock
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * 
 * Security Features:
 * - Mass assignment protection via $fillable
 * - Type casting for data integrity
 * - Unique constraint on name field (enforced at validation layer)
 * 
 * @package App\Models
 */
class Product extends Model
{
    /**
     * The attributes that are mass assignable.
     * 
     * SECURITY: Only these fields can be filled via create() or update() methods.
     * This prevents mass assignment vulnerabilities where attackers could
     * modify protected fields like 'id' or timestamps.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'amount',
        'qty',
    ];

    /**
     * The attributes that should be cast.
     * 
     * Type casting ensures data integrity:
     * - amount: Cast to decimal with 2 precision for accurate financial calculations
     * - qty: Cast to integer for stock counting
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'qty' => 'integer',
    ];
}

