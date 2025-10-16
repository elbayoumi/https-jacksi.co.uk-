<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientStoreRequest;
use App\Http\Requests\ClientUpdateRequest;
use App\Models\Client;
use App\Models\Seller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\ClientRepositoryInterface;

/**
 * Class ClientController
 *
 * Seller-facing controller for managing Clients.
 * - Uses the dedicated "seller" guard to isolate sessions/permissions.
 * - Keeps controllers thin by delegating persistence to a Repository.
 * - Returns Blade for standard web requests and JSON for API/AJAX consumers.
 *
 * Requirements/Assumptions:
 * - Multi-Guard is configured with a "seller" guard and Seller model/table.
 * - Client model exists with a 'seller_id' FK referencing sellers.id.
 * - FormRequests: ClientStoreRequest & ClientUpdateRequest handle validation.
 * - A ClientRepositoryInterface is bound to a concrete implementation in AppServiceProvider.
 */
class ClientController extends Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    protected ClientRepositoryInterface $clients;

    /**
     * Inject repository and protect all routes with the seller guard.
     */
    public function __construct(ClientRepositoryInterface $clients)
    {
        $this->middleware('auth:seller');
        $this->clients = $clients;
    }

    /**
     * Get the currently authenticated seller.
     */
    protected function seller(): Seller
    {
        /** @var Seller $seller */
        $seller = Auth::guard('seller')->user();
        return $seller;
    }

    /**
     * List the seller's clients (paginated).
     *
     * Supports HTML (Blade) and JSON responses based on the request Accept header.
     */
    public function index(Request $request): View|JsonResponse
    {
        $seller = $this->seller();

        $q = Client::query()
            ->where('seller_id', $seller->id)
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $q->items(),
                'meta' => [
                    'current_page' => $q->currentPage(),
                    'last_page'    => $q->lastPage(),
                    'per_page'     => $q->perPage(),
                    'total'        => $q->total(),
                ],
            ]);
        }

        return view('seller.clients.index', ['clients' => $q]);
    }

    /**
     * Show the create client form.
     */
    public function create(): View
    {
        return view('seller.clients.create');
    }

    /**
     * Persist a new client for the current seller.
     *
     * The repository handles persistence. The FormRequest guarantees valid data.
     */
    public function store(ClientStoreRequest $request): RedirectResponse|JsonResponse
    {
        $seller = $this->seller();

        $client = $this->clients->createForSeller($seller, $request->validated());

        if ($request->wantsJson()) {
            return response()->json(['data' => $client], 201);
        }

        return redirect()
            ->route('seller.clients.show', $client)
            ->with('status', 'Client created successfully.');
    }

    /**
     * Display a single client.
     *
     * Enforces ownership: seller can only view their own clients.
     */
    public function show(Client $client, Request $request): View|JsonResponse
    {
        $this->authorizeOwnership($client, $this->seller()->id);

        if ($request->wantsJson()) {
            return response()->json(['data' => $client]);
        }

        return view('seller.clients.show', compact('client'));
    }

    /**
     * Show the edit form for an existing client (ownership enforced).
     */
    public function edit(Client $client): View
    {
        $this->authorizeOwnership($client, $this->seller()->id);

        return view('seller.clients.edit', compact('client'));
    }

    /**
     * Update an existing client (ownership enforced).
     */
    public function update(ClientUpdateRequest $request, Client $client): RedirectResponse|JsonResponse
    {
        $this->authorizeOwnership($client, $this->seller()->id);

        $client->update($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['data' => $client->fresh()]);
        }

        return redirect()
            ->route('seller.clients.show', $client)
            ->with('status', 'Client updated successfully.');
    }

    /**
     * Delete a client (soft delete optional).
     * Consider preventing deletion if the client has invoices, or implement soft deletes.
     */
    public function destroy(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        $this->authorizeOwnership($client, $this->seller()->id);

        // If you have constraints (e.g., invoices), enforce them here or use soft deletes.
        $client->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Client deleted.']);
        }

        return redirect()
            ->route('seller.clients.index')
            ->with('status', 'Client deleted successfully.');
    }

    /**
     * Ownership guard: ensure the given client belongs to the seller.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException (403) on mismatch
     */
    protected function authorizeOwnership(Client $client, int $sellerId): void
    {
        if ((int) $client->seller_id !== $sellerId) {
            abort(403, 'You are not allowed to access this client.');
        }
    }
}
