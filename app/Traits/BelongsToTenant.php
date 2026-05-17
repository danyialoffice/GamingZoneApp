<?php

namespace App\Traits;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the trait
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically add tenant_id to all queries
        static::addGlobalScope('tenant', function (Builder $builder) {
            if ($tenant = Tenant::current()) {
                $builder->where($builder->getModel()->getTable() . '.tenant_id', $tenant->id);
            }
        });

        // Automatically set tenant_id when creating
        static::creating(function (Model $model) {
            if (!$model->tenant_id && $tenant = Tenant::current()) {
                $model->tenant_id = $tenant->id;
            }
        });
    }

    /**
     * Get the tenant that owns this model
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope query to exclude tenant filter
     */
    public function scopeWithoutTenant(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tenant');
    }

    /**
     * Scope query to specific tenant
     */
    public function scopeForTenant(Builder $query, int $tenantId): Builder
    {
        return $query->withoutGlobalScope('tenant')
                     ->where($this->getTable() . '.tenant_id', $tenantId);
    }
}
