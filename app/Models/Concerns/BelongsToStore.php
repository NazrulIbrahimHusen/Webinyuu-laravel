<?php

namespace App\Models\Concerns;

use App\Models\Store;
use App\Scopes\StoreScope;

trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        static::addGlobalScope(new StoreScope);

        static::creating(function ($model) {
            if (empty($model->store_id) && StoreScope::$storeId !== null) {
                $model->store_id = StoreScope::$storeId;
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
