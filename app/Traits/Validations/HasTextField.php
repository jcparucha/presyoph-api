<?php

namespace App\Traits\Validations;

use App\Rules\AlphaCharNumSpace;
use App\Traits\AssertionTrait;
use Exception;
use InvalidArgumentException;

trait HasTextField
{
    use AssertionTrait;

    /**
     * @param integer $min
     * @param integer $max
     * @param boolean $isRequired
     * @param string $alphaRule = 'AlphaSpace', 'AlphaNumSpace', 'AlphaCharNumSpace'
     * @return array
     */
    public function nameRule(
        int $min = 3,
        int $max = 50,
        bool $isRequired = true,
        string $alphaRule = 'AlphaCharNumSpace',
    ): array {
        $this->assertShouldBeInArray(
            ['AlphaSpace', 'AlphaNumSpace', 'AlphaCharNumSpace'],
            $alphaRule,
        );

        $ruleClass = 'App\\Rules\\' . $alphaRule;

        $this->assertShouldClassExists($ruleClass);

        return [
            $isRequired ? 'required' : 'sometimes',
            'min:' . $min,
            'max:' . $max,
            new $ruleClass(),
        ];
    }

    public function descriptionRule(
        int $min = 3,
        int $max = 255,
        bool $isRequired = true,
        bool $isNullable = false,
    ): array {
        return array_filter([
            $isRequired ? 'required' : 'sometimes',
            $isNullable ? 'nullable' : '',
            'min:' . $min,
            'max:' . $max,
            new AlphaCharNumSpace(),
        ]);
    }
}
