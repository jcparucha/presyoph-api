<?php

namespace App;

use InvalidArgumentException;

trait AssertionTrait
{
    /**
     * Check the array if it has the required fields.
     *
     * @param array $requiredKeys
     * @param array $array
     * @return void
     * @throws InvalidArgumentException
     */
    public function assertRequiredKeys(array $requiredKeys, array $array)
    {
        $missingFields = array_diff($requiredKeys, array_keys($array));

        if (count($missingFields)) {
            throw new InvalidArgumentException(
                'Missing required keys: ' . join(', ', $missingFields),
            );
        }
    }

    /**
     * Check if the given value is integer.
     *
     * @param mixed $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function assertIsInt(mixed $value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                'The given value is not integer format.',
            );
        }
    }
}
