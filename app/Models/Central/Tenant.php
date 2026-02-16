<?php

namespace App\Models\Central;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Contracts\TenantWithDatabase;

/**
 * @method static \App\Models\Central\Tenant find(string $id)
 * @method static \App\Models\Central\Tenant make(array $attributes = [])
 * @method static \App\Models\Central\Tenant create(array $attributes = [])
 * @method void setInternal(string $key, mixed $value)
 * @method void save()
 * @method void delete()
 * @method mixed run(callable $callback)
 * @property-read \Illuminate\Database\Eloquent\Collection|\Stancl\Tenancy\Database\Models\Domain[] $domains
 * @property string $id
 * @property string $service_type
 * @property int|null $subscription_period
 * @property string|null $next_payment_date
 * @property bool $is_paid
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $data
 * @property-read int|null $domains_count
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Stancl\Tenancy\Database\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDomains, HasDatabase;

    public function paymentNotifications()
    {
        return $this->hasMany(CentralPaymentNotification::class);
    }
}
