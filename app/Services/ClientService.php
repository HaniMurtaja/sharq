<?php
namespace App\Services;

use App\Models\User;
use App\Traits\HandlesSoftDelete;

class ClientService
{
    use HandlesSoftDelete;

    public function deleteClientById($id): void
    {
        $client = User::findOrFail($id);
        $this->softDeleteWithMeta($client);
    }
}
