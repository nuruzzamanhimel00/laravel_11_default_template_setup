<?php
namespace App\Traits\Scopes;


trait Filterable
{
    public function scopeFilterByStatus($query, $status = null)
    {
        if ($status === STATUS_ACTIVE) {
            return $query->active();
        } elseif ($status === STATUS_INACTIVE) {
            return $query->inactive();
        }
        return $query;
    }

    public static function getFilteredData($status = null, $paginate = 20)
    {
        // Swap parameters if first parameter is numeric
        if (is_numeric($status)) {
            [$status, $paginate] = [$paginate, $status];
        }

        return static::latest()->filterByStatus($status)->paginate($paginate);
    }
}
