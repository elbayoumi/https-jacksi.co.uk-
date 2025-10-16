<?php

namespace App\Contracts;

use App\Models\Seller;
use App\Models\Invoice;

interface InvoiceServiceInterface
{
    /**
     * Create a new invoice for the given seller.
     *
     * @param Seller $seller  The seller creating the invoice.
     * @param array $data     Validated data from the form request.
     * @return Invoice
     */
    public function createInvoice(Seller $seller, array $data): Invoice;

    /**
     * Update an existing invoice with new data.
     *
     * @param Seller $seller  The owner of the invoice.
     * @param Invoice $invoice  The invoice instance to update.
     * @param array $data  Validated request data.
     * @return Invoice
     */
    public function updateInvoice(Seller $seller, Invoice $invoice, array $data): Invoice;

    /**
     * Delete an existing invoice (and its items).
     *
     * @param Seller $seller
     * @param Invoice $invoice
     * @return void
     */
    public function deleteInvoice(Seller $seller, Invoice $invoice): void;
}
