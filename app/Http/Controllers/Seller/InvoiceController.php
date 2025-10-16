<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceStoreRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\InvoiceServiceInterface;

/**
 * Class InvoiceController
 *
 * Seller-facing controller for managing invoices.
 * - Uses the "seller" guard (session) for web access.
 * - Keeps controllers thin by delegating business logic to a service layer.
 * - Returns Blade views for standard web requests and JSON for API/AJAX.
 *
 * Assumptions:
 * - You have a separate Seller guard/provider/table.
 * - Models: Seller, Client, Invoice, InvoiceItem exist with proper relations.
 * - FormRequests: InvoiceStoreRequest, InvoiceUpdateRequest handle validation.
 * - The InvoiceServiceInterface is bound to a concrete implementation in AppServiceProvider.
 */
class InvoiceController extends Controller
{
    /**
     * @var InvoiceServiceInterface
     */
    protected InvoiceServiceInterface $service;

    /**
     * Inject the invoice service.
     */
    public function __construct(InvoiceServiceInterface $service)
    {
        $this->middleware('auth:seller'); // Ensure only authenticated sellers access
        $this->service = $service;
    }

    /**
     * Get the currently authenticated seller user.
     *
     * @return \App\Models\Seller
     */
    protected function seller()
    {
        // Using the dedicated seller guard to avoid role-mixing
        return Auth::guard('seller')->user();
    }

    /**
     * Display a paginated list of the seller's invoices.
     *
     * Supports HTML and JSON responses based on the request "Accept" header.
     */
    public function index(Request $request): View|\Illuminate\Http\JsonResponse
    {
        $seller = $this->seller();

        // Eager-load client and items summary for table display; order by latest first.
        $invoices = Invoice::with(['client:id,name', 'items:id,invoice_id'])
            ->where('seller_id', $seller->id)
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
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

        return view('seller.invoices.index', compact('invoices'));
    }

    /**
     * Show the invoice creation form.
     *
     * Loads only the current seller's clients for selection.
     */
    public function create(Request $request): View
    {
        $seller = $this->seller();

        // Fetch clients belonging to the current seller
        $clients = Client::query()
            ->where('seller_id', $seller->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('seller.invoices.create', compact('clients'));
    }

    /**
     * Store a newly created invoice using the service layer.
     *
     * The FormRequest ensures inputs are validated.
     * The service is responsible for computing totals, generating invoice number,
     * persisting items, and firing domain events.
     */
    public function store(InvoiceStoreRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $seller = $this->seller();

        // Service requires the acting user and the validated payload
        $invoice = $this->service->createInvoice($seller, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['data' => $invoice->load('client', 'items')], 201);
        }

        return redirect()
            ->route('seller.invoices.show', $invoice)
            ->with('status', 'Invoice created successfully.');
    }

    /**
     * Display a single invoice details page.
     *
     * Enforces ownership: a seller can only view their own invoices.
     */
    public function show(Invoice $invoice, Request $request): View|\Illuminate\Http\JsonResponse
    {
        $seller = $this->seller();
        $this->authorizeInvoiceOwnership($invoice, $seller->id);

        $invoice->load(['client', 'items', 'seller']);

        if ($request->wantsJson()) {
            return response()->json(['data' => $invoice]);
        }

        return view('seller.invoices.show', compact('invoice'));
    }

    /**
     * Show the edit form for an existing invoice.
     *
     * Note: Many production systems limit what can be edited on an invoice
     * once issued (e.g., items locked). Adjust as needed.
     */
    public function edit(Invoice $invoice): View
    {
        $seller = $this->seller();
        $this->authorizeInvoiceOwnership($invoice, $seller->id);

        $clients = Client::query()
            ->where('seller_id', $seller->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $invoice->load(['client', 'items']);

        return view('seller.invoices.edit', compact('invoice', 'clients'));
    }

    /**
     * Update an existing invoice via the service layer.
     *
     * The service should encapsulate business rules (e.g., recalculating totals).
     */
    public function update(InvoiceUpdateRequest $request, Invoice $invoice): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $seller = $this->seller();
        $this->authorizeInvoiceOwnership($invoice, $seller->id);

        $invoice = $this->service->updateInvoice($seller, $invoice, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['data' => $invoice->load('client', 'items')]);
        }

        return redirect()
            ->route('seller.invoices.show', $invoice)
            ->with('status', 'Invoice updated successfully.');
    }

    /**
     * Delete an invoice and its items in a transaction-safe manner.
     *
     * Consider using soft deletes if business requires auditability.
     */
    public function destroy(Request $request, Invoice $invoice): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $seller = $this->seller();
        $this->authorizeInvoiceOwnership($invoice, $seller->id);

        $this->service->deleteInvoice($seller, $invoice);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Invoice deleted.']);
        }

        return redirect()
            ->route('seller.invoices.index')
            ->with('status', 'Invoice deleted successfully.');
    }

    /**
     * Guarded ownership check to ensure the seller can only access their own invoices.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException if ownership fails.
     */
    protected function authorizeInvoiceOwnership(Invoice $invoice, int $sellerId): void
    {
        if ((int) $invoice->seller_id !== $sellerId) {
            abort(403, 'You are not allowed to access this invoice.');
        }
    }
}
