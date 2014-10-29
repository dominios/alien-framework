<?php

namespace Alien\Controllers;


use Alien\Response;
use Alien\Table\DataTable;
use Alien\View;

class ScheduleController extends BuildingController {

    protected function initialize() {
        return parent::initialize();
    }

    protected function view() {

        $view = new View('display/schedule/calendar.php');

        return new Response(array(
                'Title' => 'Týždenný rozvrh',
                'ContentMain' => $view->renderToString()
            )
        );
    }

} 