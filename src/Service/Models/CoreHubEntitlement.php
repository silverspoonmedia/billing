<?php

namespace App\Domain\Service\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Mirror of the core hub's paid layer for one identity and one product.
 *
 * Written only from an inbound hub push (`/internal/v1/entitlement/change`) and
 * never from a consumer's own code path: the money math lives in the hub, and a
 * consumer that recomputes it has two sources of truth. Read it through
 * `CoreHubEntitlementRepository`, which caches the newest row per identity.
 */
class CoreHubEntitlement extends Model
{
    use HasUuids;

    protected $table = 'core_hub_entitlements';

    protected $fillable = [
        'external_id',
        'product',
        'code',
        'tier',
        'status',
        'valid_from',
        'valid_to',
        'quantity',
        'features',
        'metadata',
        'version',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
            'quantity' => 'integer',
            'features' => 'array',
            'metadata' => 'array',
            'version' => 'integer',
            'synced_at' => 'datetime',
        ];
    }
}
