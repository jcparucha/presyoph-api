<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    public function establishments(): HasMany
    {
        return $this->hasMany(Establishment::class);
    }

    public function munCity(): BelongsTo
    {
        return $this->belongsTo(MunCity::class);
    }
}
