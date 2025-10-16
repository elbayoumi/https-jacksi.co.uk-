<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Client
 *
 * Represents a seller’s customer record in the system.
 * Each client belongs to a single seller and may have many invoices.
 *
 * Relationships:
 * - belongsTo: Seller
 * - hasMany: Invoice
 *
 * Typical usage:
 *   $seller->clients()->create([...]);
 *   $client->invoices;
 */
class Client extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'name',
        'email',
        'phone',
        'address',
    ];

    /**
     * Define the relationship with Seller (parent).
     *
     * A client belongs to one seller.
     */
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * Define the relationship with Invoices.
     *
     * A client may have multiple invoices.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Example of a local scope to filter clients by seller.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $sellerId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }
}
