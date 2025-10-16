<?php

namespace App\Repositories;

use App\Contracts\ClientRepositoryInterface;
use App\Models\Client;
use App\Models\Seller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ClientRepository
 *
 * Handles persistence and retrieval of Client entities.
 * Keeps database logic isolated from controllers and services.
 */
class ClientRepository implements ClientRepositoryInterface
{
    /**
     * Create a new client record for a given seller.
     */
    public function createForSeller(Seller $seller, array $data): Client
    {
        return DB::transaction(function () use ($seller, $data) {
            $client = new Client();
            $client->seller_id = $seller->id;
            $client->name = $data['name'];
            $client->email = $data['email'] ?? null;
            $client->phone = $data['phone'] ?? null;
            $client->address = $data['address'] ?? null;
            $client->save();

            return $client;
        });
    }

    /**
     * Get all clients for a seller (paginated or full collection).
     */
    public function getForSeller(Seller $seller, ?int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = Client::query()->where('seller_id', $seller->id)->orderBy('name');

        return $perPage
            ? $query->paginate($perPage)
            : $query->get();
    }

    /**
     * Find a specific client for a given seller.
     */
    public function findForSeller(Seller $seller, int $clientId): ?Client
    {
        return Client::where('seller_id', $seller->id)->where('id', $clientId)->first();
    }

    /**
     * Update an existing client record.
     */
    public function update(Client $client, array $data): Client
    {
        $client->update([
            'name'    => $data['name'] ?? $client->name,
            'email'   => $data['email'] ?? $client->email,
            'phone'   => $data['phone'] ?? $client->phone,
            'address' => $data['address'] ?? $client->address,
        ]);

        return $client->fresh();
    }

    /**
     * Delete a client record.
     *
     * Consider soft deletes if needed for auditability.
     */
    public function delete(Client $client): bool
    {
        return (bool) $client->delete();
    }
}
