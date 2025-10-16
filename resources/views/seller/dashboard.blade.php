@extends('layouts.seller')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    {{-- KPIs --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Invoices</div>
        <div class="text-2xl font-semibold">{{ $kpis['total_invoices'] ?? 0 }}</div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-500">Total Revenue</div>
        <div class="text-2xl font-semibold">{{ number_format($kpis['total_revenue'] ?? 0, 2) }}</div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-sm text-gray-500">Clients</div>
        <div class="text-2xl font-semibold">{{ $kpis['clients_count'] ?? 0 }}</div>
    </div>
</div>

{{-- Top Clients --}}
<div class="mt-6 bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Top Clients</h3>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2">Client</th>
                <th class="py-2">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($top_clients as $c)
                <tr class="border-b">
                    <td class="py-2">{{ $c['client_name'] ?? '—' }}</td>
                    <td class="py-2">{{ number_format($c['revenue'] ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="py-2 text-gray-500">No data yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Recent Invoices --}}
<div class="mt-6 bg-white p-6 rounded-lg shadow">
    <h3 class="text-lg font-semibold mb-4">Recent Invoices</h3>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2">#</th>
                <th class="py-2">Client</th>
                <th class="py-2">Total</th>
                <th class="py-2">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recent_invoices as $inv)
                <tr class="border-b">
                    <td class="py-2">{{ $inv->number }}</td>
                    <td class="py-2">{{ $inv->client->name ?? '—' }}</td>
                    <td class="py-2">{{ number_format($inv->total, 2) }}</td>
                    <td class="py-2">{{ $inv->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-2 text-gray-500">No invoices yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
