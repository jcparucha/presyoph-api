<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class AlphaCharNumSpace implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        if (!preg_match("/^[a-zA-Z0-9'_\- ]+$/", $value)) {
            $fail(
                'The :attribute field must only contain letters, numbers, single qoute, hypen, and underscore.',
            );
        }
    }
}
