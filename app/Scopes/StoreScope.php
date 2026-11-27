<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Scope isolasi data seller. Hanya aktif setelah middleware EnsureSeller
 * mengisi $storeId, jadi halaman publik dan panel admin tidak ikut terfilter.
 */
class StoreScope implements Scope
{
    public static ?int $storeId = null;

    public function apply(Builder $builder, Model $model): void
    {
        if (static::$storeId !== null) {
            $builder->where($model->getTable().'.store_id', static::$storeId);
        }
    }
}
