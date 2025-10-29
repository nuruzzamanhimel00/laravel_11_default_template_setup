<?php

namespace App\Traits\Scopes;

trait ScopeStatus
{
    /**
     * scopeActive
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeActive($query)
    {
        return $query->where('status', STATUS_ACTIVE);
    }
    /**
     * scopeInactive
     *
     * @param  mixed $query
     * @return void
     */
    public function scopeInactive($query)
    {
        return $query->where('status', STATUS_INACTIVE);
    }
}
