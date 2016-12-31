<?php

return [
    //-doctrine configuration
    'doctrine' => [
        'port' => '',
        'host' => 'localhost',
        'database' => 'limpid_test',
        'user' => 'root',
        'password' => '',
    ],

    //-translator configuration
    'translator' => [
        'enabled' => false,
        'default_lang' => 'fr',
    ],

    //--error pages manager
    'errors' => [
        '404' => '', //'MyModule:404error.html.twig',
        '500' => '', //'MyModule:500error.html.twig'
    ],

    //-services configuration
    'services' => [
//        example
//        'service_name' => [
//            'class' => 'app\TestModule\Service\MyService',
//        ]
    ],
];