<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InvoiceItem
 *
 * Represents a single line item in an invoice.
 * Each item belongs to exactly one invoice and contains
 * product details, quantity, unit price, and denormalized total.
 *
 * Table columns:
 *  - id (bigint)
 *  - invoice_id (FK -> invoices.id)
 *  - product_name (string)
 *  - quantity (unsigned int)
 *  - price (decimal 12,2)
 *  - total (decimal 12,2) → (quantity * price)
 *  - timestamps
 *
 * Design goals:
 *  - Keep the model lightweight — no heavy calculations here.
 *  - Use proper casts for money and quantity precision.
 *  - Allow safe recalculation helpers (without side-effects).
 */
class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     *
     * Only attributes that make sense to set from the service layer.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'product_name',
        'quantity',
        'price',
        'total',
    ];

    /**
     * Attribute casts.
     *
     * 'decimal' returns strings to preserve precision — cast to float
     * only in logic where arithmetic operations are required.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'price'    => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /* =========================================================================
     |  Relationships
     | ========================================================================= */

    /**
     * Each item belongs to exactly one invoice.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /* =========================================================================
     |  Accessors / Mutators
     | ========================================================================= */

    /**
     * Automatically compute 'total' when price or quantity changes (optional).
     *
     * Can be called manually or used in a saving() model event.
     */
    public function computeTotal(): string
    {
        $total = (float) $this->quantity * (float) $this->price;
        return number_format($total, 2, '.', '');
    }

    /**
     * Mutator example: enforce recalculation if total not set manually.
     *
     * Uncomment the boot() block below if you prefer automatic consistency.
     */
    /*
    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            if (empty($item->total) || $item->isDirty(['price', 'quantity'])) {
                $item->total = $item->computeTotal();
            }
        });
    }
    */

    /* =========================================================================
     |  Scopes
     | ========================================================================= */

    /**
     * Scope: quickly fetch items for a given invoice ID.
     */
    public function scopeOfInvoice($query, int $invoiceId)
    {
        return $query->where('invoice_id', $invoiceId);
    }

    /* =========================================================================
     |  Helpers
     | ========================================================================= */

    /**
     * Recalculate and persist the total safely.
     *
     * @param bool $save Whether to persist to DB immediately.
     * @return string The recalculated total.
     */
    public function recalcTotal(bool $save = false): string
    {
        $total = $this->computeTotal();

        if ($save) {
            $this->update(['total' => $total]);
        }

        return $total;
    }
}
