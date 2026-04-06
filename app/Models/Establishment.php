<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Establishment extends Model
{
    use SoftDeletes;

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class);
    }

    public function storeType(): BelongsTo
    {
        return $this->belongsTo(StoreType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
