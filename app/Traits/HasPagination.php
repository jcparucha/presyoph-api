<?php

namespace App\Traits;

use Illuminate\Validation\Rule;

trait HasPagination
{
    public function paginationRules(): array
    {
        return [
            'page' => ['integer', 'numeric', 'min:1'],
            'per_page' => [
                'integer',
                'numeric',
                Rule::in(config('api.pagination.per_page_sizes')),
            ],
        ];
    }
}
