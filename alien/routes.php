<?php

return array(
    'routes' => array(
        'kontakt' => array(
            'controller' => 'FrontController',
            'namespace' => 'Alien\Controllers',
            'action' => 'kontakt',
            'query' => false,
        ),
        'default' => array(
            'prefix' => 'alien',
            'pattern' => '{controller}/{action}/{query}',
            'controller' => '{name}Controller',
            'namespace' => 'Alien\Controllers',
            'action' => '{name}',
            'query' => false
        )
    )
);