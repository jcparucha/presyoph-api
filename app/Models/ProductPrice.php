<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPrice extends Model
{
    use SoftDeletes;

    protected $table = 'product_prices';

    protected $fillable = [
        'added_by',
        'product_id',
        'establishment_id',
        'price',
    ];

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(Establishment::class);
    }

    public function groceryListItems(): HasMany
    {
        return $this->hasMany(GroceryListItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // TODO Enhancement: Filtered by default place
    public function scopeLatestPerEstablishment(Builder $query): void
    {
        $query->whereIn('id', function ($query) {
            $query
                ->selectRaw('MAX(id)')
                ->from('product_prices')
                ->groupBy('product_id', 'establishment_id');
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['price' => 'float'];
    }
}
