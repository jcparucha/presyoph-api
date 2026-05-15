<?php

namespace App\Policies;

use App\Models\Establishment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EstablishmentPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Establishment $establishment): Response
    {
        return $user->id === $establishment->added_by &&
            !is_null($establishment->added_by)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
