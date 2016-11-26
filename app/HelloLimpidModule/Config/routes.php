<?php

return [
    'welcome_limpid' => [
        'pattern' => '/',
        'command' => 'HelloLimpid_Default:hello',
    ],
    'dynamic_route' => [
        'pattern' => '/article/%slug',
        'command' => 'HelloLimpid_Default:dynamic',
    ],
];