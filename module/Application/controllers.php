<?php

return [
    'controllers' => [
        'Application\Controllers\IndexController' => [
            'prepareView' => function($action) {
                return new \Alien\Mvc\View(__DIR__ . '/views/index/' . str_replace('Action', '', $action) . '.phtml');
            },
            'components' => [
                'nav' => function() {
                    return new \Alien\Mvc\Component\NavigationComponent([
                        'Home' => '#',
                        'Projects' => '#',
                        'Services' => '#',
                        'Downloads' => '#',
                        'About' => '#',
                        'Contact' => '#',
                    ]);
                }
            ],
        ]
    ],
];
