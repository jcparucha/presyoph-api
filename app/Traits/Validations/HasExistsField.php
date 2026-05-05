<?php

namespace App\Traits\Validations;

use App\Traits\AssertionTrait;

trait HasExistsField
{
    use AssertionTrait;

    public function existsRule(
        string $table,
        string $column = 'id',
        bool $isRequired = true,
    ): array {
        $this->assertShouldNotBeNull($table);

        return [
            $isRequired ? 'required' : 'sometimes',
            'exists:' . $table . ',' . $column,
        ];
    }
}
