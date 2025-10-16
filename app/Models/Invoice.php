<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Invoice
 *
 * Domain model representing a seller-issued invoice. This model remains intentionally
 * lean and free of business logic; complex calculations and side effects (e.g. events,
 * notifications) should live in the Service layer.
 *
 * Table columns (from migration):
 *  - id (bigint)
 *  - seller_id (FK -> sellers.id)
 *  - client_id (FK -> clients.id)
 *  - number (string, unique)
 *  - subtotal (decimal(12,2))
 *  - tax (decimal(12,2))
 *  - total (decimal(12,2))
 *  - timestamps
 *
 * Relationships:
 *  - seller(): Seller (owner)
 *  - client(): Client (billed party)
 *  - items():  InvoiceItem[] (line items)
 *
 * Helpful features:
 *  - Typed casts for money fields (decimal:2).
 *  - Common query scopes: ofSeller(), betweenDates(), withSearch().
 *  - Accessors for quick aggregates: items_count, total_quantity.
 *  - Recalculation helpers: recalcSubtotal(), recalcTotals().
 */
class Invoice extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     *
     * Keep this explicit to avoid accidental mass-assignment.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'seller_id',
        'client_id',
        'number',
        'subtotal',
        'tax',
        'total',
    ];

    /**
     * Attribute casting.
     *
     * Note: Laravel's 'decimal' cast returns strings to preserve precision.
     * For math operations, cast to float/decimal in code as needed.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax'      => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /**
     * Appended attributes when converting to array/JSON.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'items_count',
        'total_quantity',
    ];

    /* =========================================================================
     |  Relationships
     | ========================================================================= */

    /**
     * The seller who owns this invoice.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    /**
     * The client this invoice is billed to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Line items of this invoice.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /* =========================================================================
     |  Accessors (Computed Attributes)
     | ========================================================================= */

    /**
     * Number of items (rows) on the invoice.
     */
    public function getItemsCountAttribute(): int
    {
        // If relation is loaded, avoid extra query.
        if ($this->relationLoaded('items')) {
            return $this->items->count();
        }
        return $this->items()->count();
    }

    /**
     * Total quantity (sum of quantities across all items).
     */
    public function getTotalQuantityAttribute(): int
    {
        if ($this->relationLoaded('items')) {
            return (int) $this->items->sum('quantity');
        }
        return (int) $this->items()->sum('quantity');
    }

    /* =========================================================================
     |  Query Scopes
     | ========================================================================= */

    /**
     * Scope: filter invoices by seller ownership.
     */
    public function scopeOfSeller($query, int $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Scope: filter by created_at window (inclusive).
     */
    public function scopeBetweenDates($query, ?string $fromDate, ?string $toDate)
    {
        if ($fromDate) {
            $query->where('created_at', '>=', $fromDate);
        }
        if ($toDate) {
            $query->where('created_at', '<=', $toDate);
        }
        return $query;
    }

    /**
     * Scope: simple search by invoice number or client name.
     * Requires `client` relation join/eager load for name search.
     */
    public function scopeWithSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        $like = '%' . str_replace('%', '\\%', trim($term)) . '%';

        return $query->where(function ($q) use ($like) {
            $q->where('number', 'like', $like)
              ->orWhereHas('client', fn ($cq) => $cq->where('name', 'like', $like));
        });
    }

    /* =========================================================================
     |  Helpers (No side-effects)
     | ========================================================================= */

    /**
     * Recalculate subtotal by summing all items (quantity * price).
     *
     * IMPORTANT:
     *  - Does NOT persist changes to database.
     *  - Business rules (discounts/tax strategies) should live in a Service.
     */
    public function recalcSubtotal(): string
    {
        // Sum as float then format to string with 2 decimals to match 'decimal:2'
        $sum = (float) $this->items()->selectRaw('SUM(quantity * price) as s')->value('s') ?: 0.0;

        // Return formatted string for consistency with decimal casts
        return number_format($sum, 2, '.', '');
    }

    /**
     * Recalculate totals using the current subtotal and provided tax amount.
     *
     * @param  string|float|int|null  $taxAmount  Absolute tax amount (not a rate).
     * @return array{subtotal:string,tax:string,total:string} Calculated amounts (not persisted).
     */
    public function recalcTotals(string|float|int|null $taxAmount = null): array
    {
        $subtotal = (float) $this->recalcSubtotal();
        $tax = $taxAmount !== null ? (float) $taxAmount : (float) $this->tax;
        $total = $subtotal + $tax;

        return [
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'tax'      => number_format($tax, 2, '.', ''),
            'total'    => number_format($total, 2, '.', ''),
        ];
    }

    /* =========================================================================
     |  Static Utilities (Optional)
     | ========================================================================= */

    /**
     * Generate a candidate invoice number. Keep uniqueness checks in Service.
     *
     * Example: INV-20251016-00042
     */
    public static function makeNumber(int $sequence = 1): string
    {
        return sprintf('INV-%s-%05d', now()->format('Ymd'), $sequence);
    }
}
