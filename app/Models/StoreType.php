<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreType extends Model
{
    public function establishments(): HasMany
    {
        return $this->hasMany(Establishment::class);
    }
}
