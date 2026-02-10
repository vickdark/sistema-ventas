<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * @method static \App\Models\Tenant find(string $id)
 * @method static \App\Models\Tenant make(array $attributes = [])
 * @method static \App\Models\Tenant create(array $attributes = [])
 * @method void setInternal(string $key, mixed $value)
 * @method void save()
 * @method void delete()
 * @method mixed run(callable $callback)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stancl\Tenancy\Database\Models\Domain[] $domains
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDomains, HasDatabase;
}
