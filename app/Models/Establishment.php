<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Establishment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'barangay_code',
        'store_type_id',
        'added_by',
    ];

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function barangay(): BelongsTo
    {
        return $this->belongsTo(Barangay::class, 'barangay_code', 'code');
    }

    public function storeType(): BelongsTo
    {
        return $this->belongsTo(StoreType::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function scopeInRegion(Builder $query, string $code): void
    {
        $query->whereHas(
            'barangay.munCity.province.region',
            $this->_whereHasCallback($code),
        );
    }

    public function scopeInProvince(Builder $query, string $code): void
    {
        $query->whereHas(
            'barangay.munCity.province',
            $this->_whereHasCallback($code),
        );
    }

    public function scopeInMunCity(Builder $query, string $code): void
    {
        $query->whereHas('barangay.munCity', $this->_whereHasCallback($code));
    }

    public function scopeInBarangay($query, string $code): void
    {
        $query->where('barangay_code', $code);
    }

    public function scopeOfStoreType($query, int $storeTypeId): void
    {
        $query->where('store_type_id', $storeTypeId);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    private function _whereHasCallback($code)
    {
        return function (Builder $query) use ($code) {
            $query->where('code', $code);
        };
    }
}
