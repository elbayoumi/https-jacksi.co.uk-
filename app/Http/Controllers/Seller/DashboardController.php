<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\DashboardIndexRequest;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class DashboardController
 *
 * Seller-facing dashboard controller that aggregates KPIs and recent activity
 * for the currently authenticated seller. This controller:
 *  - Enforces the "seller" guard.
 *  - Returns a Blade view for HTML requests.
 *  - Returns structured JSON for API/AJAX requests.
 *
 * Assumptions:
 *  - "Seller" is a dedicated guard with its own provider/table.
 *  - Invoices belong to sellers via invoices.seller_id.
 *  - Clients belong to sellers via clients.seller_id.
 *  - Invoice has relation: items() for per-invoice line items.
 */
class DashboardController extends Controller
{
    /**
     * Apply seller authentication to all actions.
     */
    public function __construct()
    {
        $this->middleware('auth:seller');
    }

    /**
     * Helper: get the currently authenticated seller user.
     *
     * @return \App\Models\Seller
     */
    protected function seller()
    {
        // Using the dedicated guard ensures we never mix roles.
        return Auth::guard('seller')->user();
    }

    /**
     * Display the seller dashboard with KPIs and recent activity.
     *
     * Accepts optional query params for time-window filtering:
     *  - from=YYYY-MM-DD
     *  - to=YYYY-MM-DD (inclusive end-of-day)
     *
     * For HTML requests -> renders Blade view: resources/views/seller/dashboard.blade.php
     * For JSON requests  -> returns a normalized payload.
     */
    public function index(DashboardIndexRequest $request): View|JsonResponse
    {
        $seller = $this->seller();

        // Optional time window filters (default: show all time).
        $from = $request->date('from'); // nullable Carbon
        $to   = $request->date('to');   // nullable Carbon
        if ($to) {
            // include entire end date (23:59:59) for inclusive filtering
            $to = $to->endOfDay();
        }

        // Base query for invoices owned by the current seller
        $invoiceQuery = Invoice::query()
            ->where('seller_id', $seller->id);

        if ($from) {
            $invoiceQuery->where('created_at', '>=', $from);
        }
        if ($to) {
            $invoiceQuery->where('created_at', '<=', $to);
        }

        // KPIs
        $totalInvoices = (clone $invoiceQuery)->count();
        $totalRevenue  = (clone $invoiceQuery)->sum('total');

        // Top clients by revenue (limit 5)
        $topClients = (clone $invoiceQuery)
            ->select('client_id', DB::raw('SUM(total) as revenue'))
            ->groupBy('client_id')
            ->orderByDesc('revenue')
            ->with(['client:id,name']) // eager load minimal fields
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'client_id' => $row->client_id,
                'client_name' => optional($row->client)->name,
                'revenue' => (float) $row->revenue,
            ]);

        // Recent invoices for the table/widget
        $recentInvoices = (clone $invoiceQuery)
            ->with(['client:id,name', 'items:id,invoice_id']) // lightweight eager-load
            ->latest('id')
            ->limit(10)
            ->get(['id','number','client_id','total','created_at']);

        // Monthly revenue series (last 6 months) for a sparkline chart
        $monthlySeries = (clone $invoiceQuery)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as ym'),
                DB::raw('SUM(total) as revenue')
            )
            ->groupBy('ym')
            ->orderBy('ym', 'asc')
            ->get()
            ->map(fn ($row) => ['month' => $row->ym, 'revenue' => (float) $row->revenue]);

        // Build a normalized payload for both HTML and JSON
        $payload = [
            'filters' => [
                'from' => $from?->toDateString(),
                'to'   => $to?->toDateString(),
            ],
            'kpis' => [
                'total_invoices' => $totalInvoices,
                'total_revenue'  => (float) $totalRevenue,
                'clients_count'  => Client::where('seller_id', $seller->id)->count(),
            ],
            'top_clients' => $topClients,
            'recent_invoices' => $recentInvoices,
            'monthly_series' => $monthlySeries,
        ];

        if ($request->wantsJson()) {
            return response()->json(['data' => $payload]);
        }

        return view('seller.dashboard', $payload);
    }
}
