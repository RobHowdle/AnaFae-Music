<?php

return [
    'recipient' => [
        'address' => env('CONTACT_FORM_TO_ADDRESS', env('MAIL_FROM_ADDRESS', 'hello@example.com')),
        'name' => env('CONTACT_FORM_TO_NAME', env('MAIL_FROM_NAME', 'Ana Fae Music')),
    ],
];