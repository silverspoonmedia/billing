<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Published view of the hub's paid layer.
 *
 * The hub is the only writer: a checkout, renewal, grace window or expiry happens
 * there, and every transition is pushed to this consumer as a new `version`.
 * This table is therefore a *mirror*, not a ledger — it is truncated and rebuilt
 * from the hub, and no consumer may compute a tier, an amount or a grace window
 * from it.
 *
 * Deliberately not foreign-keyed to `users`: the identity the hub speaks about is
 * `external_id`, a UUID the hub owns. A consumer whose local user row is missing
 * still records the entitlement, so the push is not lost while the account is
 * provisioned.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_hub_entitlements', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('external_id', 64);
            $table->string('product', 64);
            $table->string('code', 128)->default('');
            $table->string('tier', 16)->default('free');
            $table->string('status', 16)->default('expired');
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('valid_to')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->json('features')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('version')->default(0);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['external_id', 'product']);
            $table->index(['product', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_hub_entitlements');
    }
};
