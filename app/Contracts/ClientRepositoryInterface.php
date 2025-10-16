<?php

namespace App\Contracts;

use App\Models\Client;
use App\Models\Seller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ClientRepositoryInterface
{
    /**
     * Create a new client record under a specific seller.
     *
     * @param Seller $seller  The seller who owns the client.
     * @param array $data     Validated form data.
     * @return Client
     */
    public function createForSeller(Seller $seller, array $data): Client;

    /**
     * Fetch all clients belonging to the seller.
     *
     * @param Seller $seller
     * @param int|null $perPage
     * @return LengthAwarePaginator|Collection
     */
    public function getForSeller(Seller $seller, ?int $perPage = 15): LengthAwarePaginator|Collection;

    /**
     * Find a specific client by ID (ownership enforced).
     *
     * @param Seller $seller
     * @param int $clientId
     * @return Client|null
     */
    public function findForSeller(Seller $seller, int $clientId): ?Client;

    /**
     * Update a client record.
     *
     * @param Client $client
     * @param array $data
     * @return Client
     */
    public function update(Client $client, array $data): Client;

    /**
     * Delete a client record.
     *
     * @param Client $client
     * @return bool
     */
    public function delete(Client $client): bool;
}
