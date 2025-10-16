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
use App\Http\Requests\Seller\ClientIndexRequest;

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
     * Display a paginated, searchable list of clients for the current seller.
     *
     * @param ClientIndexRequest $request  Validated query parameters
     * @return View|JsonResponse
     */
    public function index(ClientIndexRequest $request): View|JsonResponse
    {
        $seller = $this->seller();

        // Extract safe, sanitized inputs from the request
        $search  = $request->search();           // already escaped for LIKE
        $sort    = $request->sort();             // whitelisted: name,email,created_at
        $order   = $request->order();            // asc|desc
        $perPage = $request->perPage();          // 10|15|25|50

        $query = Client::query()
            ->where('seller_id', $seller->id)
            ->when($search, function ($q) use ($search) {
                // Use ESCAPE for literal %/_ chars. Laravel doesn't expose ESCAPE directly,
                // but escaping the term is typically sufficient. If your driver supports it,
                // you can also use whereRaw with an ESCAPE clause.
                $like = "%{$search}%";
                $q->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                });
            })
            ->orderBy($sort, $order);

        $clients = $query
            ->paginate($perPage)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $clients->items(),
                'meta' => [
                    'current_page' => $clients->currentPage(),
                    'last_page'    => $clients->lastPage(),
                    'per_page'     => $clients->perPage(),
                    'total'        => $clients->total(),
                ],
            ]);
        }

        return view('seller.clients.index', ['clients' => $clients]);
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
