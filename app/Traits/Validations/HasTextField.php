<?php

namespace App\Traits\Validations;

use App\Rules\TextRule;
use App\Traits\AssertionTrait;

trait HasTextField
{
    use AssertionTrait;

    /**
     * @param  bool  $isNullable
     * @param  string  $type  basic | extended | descriptive | technical
     */
    public function nameRule(
        int $min = 3,
        int $max = 50,
        bool $isRequired = true,
        bool $allowSpace = true,
        bool $allowNumbers = true,
        string $type = 'basic',
    ): array {
        $specialChars = config('validation.special_chars_sets');

        $this->assertShouldBeInArray(array_keys($specialChars), $type);

        return [
            $isRequired ? 'required' : 'sometimes',
            'min:'.$min,
            'max:'.$max,
            new TextRule($allowSpace, $allowNumbers, $specialChars[$type]),
        ];
    }

    /**
     * @param  string  $type  basic | extended | descriptive | technical
     */
    public function descriptionRule(
        int $min = 3,
        int $max = 255,
        bool $isRequired = true,
        bool $isNullable = false,
        bool $allowSpace = true,
        bool $allowNumbers = true,
        string $type = 'descriptive',
    ): array {
        $specialChars = config('validation.special_chars_sets');

        $this->assertShouldBeInArray(array_keys($specialChars), $type);

        return array_filter([
            $isRequired ? 'required' : 'sometimes',
            $isNullable ? 'nullable' : '',
            'min:'.$min,
            'max:'.$max,
            new TextRule($allowSpace, $allowNumbers, $specialChars[$type]),
        ]);
    }
}
