<?php

return array(
    'login' => array(
        'route' => '/login',
        'controller' => 'BaseController',
        'namespace' => 'Alien\Controllers',
        'action' => 'loginScreen'
    ),
    'logout' => array(
        'route' => '/logout',
        'controller' => 'BaseController',
        'namespace' => 'Alien\Controllers',
        'action' => 'logout'
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
    'schedule' => array(
        'route' => '/schedule',
        'controller' => 'ScheduleController',
        'namespace' => 'Alien\Controllers',
        'childRoutes' => array(
            '' => array(
                'route' => '',
                'action' => 'calendarAction'
            ),
            'addEvent' => array(
                'route' => '/addEvent[/:courseId]',
                'action' => 'addEventAction'
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
            'filter' => array(
                'route' => '/filter[/:filter]',
                'action' => 'viewList',
            ),
            'edit' => array(
                'route' => '/edit/:id',
                'action' => 'edit'
            ),
            'remove' => array(
                'route' => '/remove/:id',
                'action' => 'remove'
            ),
            'addGroup' => array(
                'route' => '/addGroup/:ug',
                'action' => 'addGroup'
            ),
            'removeGroup' => array(
                'route' => '/removeGroup/:ug',
                'action' => 'removeGroup'
            )
        )
    )
);
