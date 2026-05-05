<?php

namespace App\Traits;

use InvalidArgumentException;

trait AssertionTrait
{
    /**
     * Check the array if it has the required keys.
     *
     * @param array $requiredKeys
     * @param array $array
     * @return void
     * @throws InvalidArgumentException
     */
    public function assertShouldHaveKeys(array $requiredKeys, array $array)
    {
        $missingFields = array_diff($requiredKeys, array_keys($array));

        if (count($missingFields)) {
            throw new InvalidArgumentException(
                'Missing required keys: ' . join(', ', $missingFields),
            );
        }
    }

    public function assertShouldBeInArray(array $haystack, mixed $needle)
    {
        if (!in_array($needle, $haystack)) {
            throw new InvalidArgumentException(
                'The ' .
                    $needle .
                    ' field is neither ' .
                    join(' or', $haystack) .
                    '.',
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
    public function assertShouldBeInteger(mixed $value)
    {
        if (!is_int($value)) {
            throw new InvalidArgumentException(
                'The given value should be an integer.',
            );
        }
    }

    /**
     * @param mixed $value
     * @return void
     * @throws InvalidArgumentException
     */
    public function assertShouldNotBeNull(mixed $value)
    {
        if (is_null($value)) {
            throw new InvalidArgumentException(
                'The given value should not be null.',
            );
        }
    }
}
