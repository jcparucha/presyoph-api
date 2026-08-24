<?php

namespace App\Facades\Actions;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\User|null handle(array $credentials)
 *
 * @see RegisterUserAction
 */
class RegisterUser extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'actions.auth.register-user';
    }
}
