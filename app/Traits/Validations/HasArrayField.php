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
     * @param  string  $alphaRule  = 'AlphaSpace', 'AlphaNumSpace', 'AlphaCharNumSpace'
     */
    public function textItemRule(
        int $max = 25,
        bool $isRequired = true,
        string $alphaRule = 'AlphaCharNumSpace',
    ): array {
        $this->assertShouldBeInArray(
            ['AlphaSpace', 'AlphaNumSpace', 'AlphaCharNumSpace'],
            $alphaRule,
        );

        return [
            ...$this->nameRule(
                max: $max,
                isRequired: $isRequired,
                alphaRule: $alphaRule,
            ),
            'distinct',
        ];
    }
}
