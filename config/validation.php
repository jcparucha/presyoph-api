<?php

return [
    // Common reusable sets
    'special_chars_sets' => [
        'basic' => ".-'", // dot, dash, apostrophe
        'extended' => ".-'&:+@", // includes ampersand, colon, plus, at
        'descriptive' => ".-'&:+@,()[]/", // for longer descriptions
        'technical' => '.-_/', // if you ever need slashes/underscores
    ],

    'text_fields' => [
        'product_name' => 'basic',
        'establishment_name' => 'extended',
        'brand_name' => 'basic',
        'category_name' => 'basic',
        'description' => 'descriptive',
        'grocery_list_name' => 'extended',
        'grocery_list_desc' => 'descriptive',
    ],
];
