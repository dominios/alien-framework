<?php

namespace Application\Controllers\Rest;

use Alien\Rest\BaseRestfulController;

class NavController extends BaseRestfulController
{

    function listAction()
    {
        $data = [
            [
                'label' => 'Home',
                'link' => 'home'
            ],
            [
                'label' => 'Projects',
                'link' => 'projects'
            ],
            [
                'label' => 'Services',
                'link' => 'services'
            ],
            [
                'label' => 'Downloads',
                'link' => 'downloads'
            ],
            [
                'label' => 'About',
                'link' => 'about'
            ],
            [
                'label' => 'Contact',
                'link' => 'contact'
            ]

        ];

        return $this->dataResponse($data);
    }

}