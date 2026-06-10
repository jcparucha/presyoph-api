<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEntitlement extends Model
{
    protected $table = 'user_entitlements';

    protected $fillable = ['user_id', 'max_grocery_lists'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
