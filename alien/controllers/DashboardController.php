<?php

namespace Alien\Controllers;

use Alien\Response;

class DashboardController extends BaseController {

    protected function init_action() {
        $this->defaultAction = 'NOP';

        $parentResponse = parent::init_action();
        if ($parentResponse instanceof Response) {
            $data = $parentResponse->getData();
        }

        return new Response(Response::RESPONSE_OK, Array(
            'LeftTitle' => 'Dashboard',
            'ContentLeft' => '',
            'MainMenu' => $data['MainMenu']
                ), __CLASS__ . '::' . __FUNCTION__);
    }

    protected function home() {

    }

    protected function messages() {
        
    }

}

