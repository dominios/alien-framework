<?php

namespace Alien\Controllers;


use Alien\Forms\Input;
use Alien\Models\School\ScheduleEventDao;
use Alien\Response;
use Alien\Table\DataTable;
use Alien\View;
use DateTime;

class ScheduleController extends BaseController {

    /**
     * @var ScheduleEventDao
     */
    protected $scheduleEventDao;

    protected function initialize() {
        parent::initialize();
        $this->scheduleEventDao = $this->getServiceManager()->getDao('ScheduleEventDao');
    }

    protected function view() {

        $view = new View('display/schedule/calendar.php');

        $addButton = Input::button($this->actionUrl("addEvent"), 'Pridať udalosť');
        $view->addButtton = $addButton;

        $data = array();
        $events = $this->scheduleEventDao->getList();
        foreach ($events as $event) {
            $data[] = array(
                'title' => $event->getCourse()->getName(),
                'start' => $event->getDateFrom(DateTime:: ISO8601),
                'end' => $event->getDateTo(DateTime::ISO8601),
                'color' => '#' . $event->getCourse()->getColor()
            );
        }

        $view->events = $data;

        return new Response(array(
                'Title' => 'Týždenný rozvrh',
                'ContentMain' => $view->renderToString()
            )
        );
    }

} 