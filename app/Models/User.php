<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['username', 'password'];

    protected function scopeRealUser(Builder $query): void
    {
        $query->withAttributes([
            'is_test_account' => false,
        ]);
    }

    protected function scopeTestUser(Builder $query): void
    {
        $query->withAttributes([
            'is_test_account' => true,
        ]);
    }

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class, 'added_by');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'added_by');
    }

    public function defaultMaxGroceryLists(): HasOne
    {
        return $this->entitlement()->withAttributes([
            'max_grocery_lists' => config('default.entitlement.max_grocery_lists'),
        ]);
    }

    public function entitlement(): HasOne
    {
        return $this->hasOne(UserEntitlement::class);
    }

    public function establishments(): HasMany
    {
        return $this->hasMany(Establishment::class, 'added_by');
    }

    public function groceryLists(): HasMany
    {
        return $this->hasMany(GroceryList::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'added_by');
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, 'added_by');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'added_by');
    }

    public function unpublishedGroceryList(): HasMany
    {
        return $this->groceryLists()->withAttributes(['is_public' => false]);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_test_account' => 'boolean',
            'created_at' => 'datetime:Y-m-d H:i:s.u',
            'updated_at' => 'datetime:Y-m-d H:i:s.u',
        ];
    }
}
