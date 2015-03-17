<?php

return array(
    'login' => array(
        'route' => '/login',
        'controller' => 'BaseController',
        'namespace' => 'Alien\Controllers',
        'action' => 'loginScreen'
    ),
    'dashboard' => array(
        'route' => '/dashboard',
        'controller' => 'DashboardController',
        'namespace' => 'Alien\Controllers',
        'action' => 'home'
    ),
    'building' => array(
        'route' => '/building',
        'controller' => 'BuildingController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            '' => array(
                'route' => '',
                'action' => 'listBuildingsAction'
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'editBuildingAction'
            ),
            'add' => array(
                'route' => '/add',
                'action' => 'addBuildingAction',
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'removeBuildingAction',
            )
        )
    ),
    'room' => array(
        'route' => 'room',
        'controller' => 'BuildingController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            '' => array(
                'route' => '',
                'action' => 'listRoomsAction'
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'editRoomAction'
            ),
            'add' => array(
                'route' => '/add',
                'action' => 'addRoomAction',
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'removeRoomAction',
            )
        )
    ),
    'course' => array(
        'route' => '/course',
        'controller' => 'CourseController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            '' => array(
                'route' => '',
                'action' => 'listAction'
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'editAction'
            ),
            'new' => array(
                'route' => '/new',
                'action' => 'newAction'
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'removeAction'
            )
        )
    ),
    'user' => array(
        'route' => '/user',
        'controller' => 'UsersController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            '' => array(
                'route' => '',
                'action' => 'viewList'
            ),
            'list' => array(
                'route' => '/list',
                'action' => 'viewList',
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'edit'
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'remove'
            )
        )
    )
);

//return array(
//    'routes' => array(
//        'kontakt' => array(
//            'controller' => 'FrontController',
//            'namespace' => 'Alien\Controllers',
//            'action' => 'kontakt',
//            'query' => false,
//        ),
//        'default' => array(
//            'prefix' => 'alien',
//            'pattern' => '{controller}/{action}/{query}',
//            'controller' => '{name}Controller',
//            'namespace' => 'Alien\Controllers',
//            'action' => '{name}',
//            'query' => false
//        )
//    )
//);