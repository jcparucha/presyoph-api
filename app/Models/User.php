<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use SoftDeletes;

    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class, 'added_by');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'added_by');
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
