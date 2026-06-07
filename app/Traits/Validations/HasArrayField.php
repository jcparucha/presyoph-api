<?php

namespace App\Traits\Validations;

use App\Traits\AssertionTrait;

trait HasArrayField
{
    use AssertionTrait, HasTextField;

    public function arrayRule(int $max = 5, bool $isRequired = true): array
    {
        return [$isRequired ? 'required' : 'sometimes', 'array', 'max:'.$max];
    }

    /**
     * @param  bool  $isDistinct
     * @param  string  $alphaRule  basic | extended | descriptive | technical
     */
    public function textItemRule(int $max = 25, bool $isRequired = true, string $type = 'basic'): array
    {
        $specialChars = config('validation.special_chars_sets');

        $this->assertShouldBeInArray(array_keys($specialChars), $type);

        return [...$this->nameRule(max: $max, isRequired: $isRequired, type: $type), 'distinct'];
    }
}
