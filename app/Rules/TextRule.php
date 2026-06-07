<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TextRule implements ValidationRule
{
    public function __construct(
        private bool $allowSpace = true,
        private bool $allowNumbers = true,
        private ?string $allowCharacters = null,
    ) {}

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $whitespace = $this->allowSpace ? '\s' : '';
        $numbers = $this->allowNumbers ? '\pN' : '';
        $characters = $this->allowCharacters ? preg_quote($this->allowCharacters, '/') : '';

        $pattern = '/^[\pL'.$numbers.$whitespace.$characters.']+$/u';

        if (! preg_match($pattern, $value)) {
            $fail($this->getMessage());
        }
    }

    private function getMessage(): string
    {
        $message = 'The :attribute field must only contain letters';

        if ($this->allowNumbers) {
            if ($this->allowSpace || ! is_null($this->allowCharacters)) {
                // message: "...contain letters, numbers"
                $message .= ', numbers';
            } else {
                // message: "...contain letters and numbers"
                $message .= ' and numbers';
            }
        }

        if ($this->allowSpace) {
            if (! is_null($this->allowCharacters)) {
                // message: "...contain letters, numbers, spaces"
                // or "...contain letters, spaces"
                $message .= ', spaces';
            } else {
                // message: "...contain letters, numbers, and spaces"
                // or "...contain letters and spaces"
                $message .= $this->allowNumbers ? ',' : '';
                $message .= ' and spaces';
            }
        }

        if (! is_null($this->allowCharacters)) {
            // message: "...contain letters, numbers, spaces, and special characters (.,-+:!?&@)"
            // or "...contain letters, numbers, and special characters (.,-+:!?&@)"
            // or "...contain letters, spaces, and special characters (.,-+:!?&@)"
            // or "...contain letters and special characters (.,-+:!?&@)"
            $message .= $this->allowNumbers || $this->allowSpace ? ',' : '';
            $message .= " and special characters ($this->allowCharacters)";
        }

        return $message .= '.';
    }
}
