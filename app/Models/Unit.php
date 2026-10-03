<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function scopeOfUnit(Builder $query, string $unit): void
    {
        $query->where('abbreviation', $unit);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
