<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\ModelBootHandler;
use App\Traits\Scopes\Filterable;
use App\Traits\Scopes\ScopeStatus;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use ScopeStatus, Filterable, ModelBootHandler;

    protected $guarded = ['id'];

    public const FILE_STORE_PATH    = 'warehouse';

    protected $casts = [
        'is_default' => 'boolean',
    ];

    protected $appends = ['status_badge'];

    protected function getStatusBadgeAttribute(): string
    {
        $badge = $this->status == STATUS_ACTIVE ? 'bg-success' : 'bg-danger';
        return '<span class="badge '.$badge.'">'.Str::upper($this->status).'</span>';
    }

    // =========== scopes ================>

    // =========== scopes ================>
}
