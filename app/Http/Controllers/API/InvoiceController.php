<?php

namespace App\Http\Controllers\API;

use App\Contracts\InvoiceServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class InvoiceController (API)
 *
 * RESTful endpoints for invoices.
 * Uses Sanctum (optional) + Multi-Guard: allows both admin and seller, but
 * applies data scoping in queries. Sellers see only their invoices. Admins see all.
 */
class InvoiceController extends Controller
{
    public function __construct(private readonly InvoiceServiceInterface $service)
    {
        // auth:sanctum is assumed for API; you can wrap routes with abilities middleware in routes/api.php
        $this->middleware('auth:sanctum');
    }

    /**
     * List invoices.
     *  - Seller: only own invoices.
     *  - Admin: all invoices.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Invoice::query()->with(['client:id,name', 'items:id,invoice_id']);

        $isAdmin  = $user->tokenCan('admin')  || method_exists($user, 'getTable') && $user->getTable() === 'admins';
        $isSeller = $user->tokenCan('seller') || method_exists($user, 'getTable') && $user->getTable() === 'sellers';

        if ($isSeller) {
            $query->where('seller_id', $user->id);
        }

        $invoices = $query->latest('id')->paginate($request->integer('per_page', 15));

        return response()->json([
            'data' => $invoices->items(),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page'    => $invoices->lastPage(),
                'per_page'     => $invoices->perPage(),
                'total'        => $invoices->total(),
            ],
        ]);
    }

    /**
     * Create a new invoice using the service.
     * Payload validated by InvoiceStoreRequest.
     */
    public function store(InvoiceStoreRequest $request): JsonResponse
    {
        $user = $request->user();
        // Only sellers can create invoices; admins usually won't do this.
        $isSeller = $user->tokenCan('seller') || (method_exists($user, 'getTable') && $user->getTable() === 'sellers');

        abort_unless($isSeller, 403, 'Only sellers may create invoices.');

        $invoice = $this->service->createInvoice($user, $request->validated());

        return response()->json(['data' => $invoice->load('client', 'items')], 201);
    }

    /**
     * Show a single invoice.
     * Ownership check for sellers; admins can view all.
     */
    public function show(Invoice $invoice, Request $request): JsonResponse
    {
        $user = $request->user();

        $isAdmin  = $user->tokenCan('admin')  || (method_exists($user, 'getTable') && $user->getTable() === 'admins');
        $isSeller = $user->tokenCan('seller') || (method_exists($user, 'getTable') && $user->getTable() === 'sellers');

        if ($isSeller && (int)$invoice->seller_id !== (int)$user->id) {
            abort(403, 'Unauthorized invoice access.');
        }

        return response()->json(['data' => $invoice->load('client', 'items', 'seller')]);
    }

    /**
     * Update an invoice (seller-owned). Admins typically shouldn't modify seller data directly.
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice): JsonResponse
    {
        $user = $request->user();
        $isSeller = $user->tokenCan('seller') || (method_exists($user, 'getTable') && $user->getTable() === 'sellers');

        abort_unless($isSeller, 403);
        abort_if((int)$invoice->seller_id !== (int)$user->id, 403, 'Unauthorized invoice update.');

        $invoice = $this->service->updateInvoice($user, $invoice, $request->validated());

        return response()->json(['data' => $invoice->load('client', 'items')]);
    }

    /**
     * Delete an invoice (seller-owned).
     */
    public function destroy(Request $request, Invoice $invoice): JsonResponse
    {
        $user = $request->user();
        $isSeller = $user->tokenCan('seller') || (method_exists($user, 'getTable') && $user->getTable() === 'sellers');

        abort_unless($isSeller, 403);
        abort_if((int)$invoice->seller_id !== (int)$user->id, 403, 'Unauthorized invoice deletion.');

        $this->service->deleteInvoice($user, $invoice);

        return response()->json(['message' => 'Invoice deleted.']);
    }
}
