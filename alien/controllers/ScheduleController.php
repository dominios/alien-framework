<?php

namespace Alien\Controllers;


use Alien\Db\RecordNotFoundException;
use Alien\Forms\Input;
use Alien\Forms\Schedule\EventForm;
use Alien\Models\School\CourseDao;
use Alien\Models\School\RoomDao;
use Alien\Models\School\ScheduleEvent;
use Alien\Models\School\ScheduleEventDao;
use Alien\Notification;
use Alien\Response;
use Alien\View;
use DateTime;

class ScheduleController extends BaseController {

    /**
     * @var ScheduleEventDao
     */
    protected $scheduleEventDao;

    /**
     * @var CourseDao
     */
    protected $courseDao;

    /**
     * @var RoomDao
     */
    protected $roomDao;

    protected function initialize() {
        parent::initialize();
        $this->scheduleEventDao = $this->getServiceManager()->getDao('ScheduleEventDao');
        $this->roomDao = $this->getServiceManager()->getDao('RoomDao');
        $this->courseDao = $this->getServiceManager()->getDao('CourseDao');
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
                'color' => '#' . $event->getCourse()->getColor(),
                'url' => $this->actionUrl('editEvent', array('id' => $event->getId()))
            );
        }

        $view->events = $data;

        return new Response(array(
                'Title' => 'Kalendár',
                'ContentMain' => $view->renderToString()
            )
        );
    }

    protected function addEvent() {

        $view = new View('display/schedule/eventForm.php');
        $event = new ScheduleEvent();
        $form = EventForm::factory($event, $this->courseDao, $this->roomDao);
        $form->getField('action', true)->setValue('schedule/addEvent');

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $df = new DateTime('@' . strtotime($_POST['eventDateFrom']));
                $dt = new DateTime('@' . strtotime($_POST['eventDateTo']));
                $course = $this->roomDao->find($_POST['eventCourse']);
                $room = $this->roomDao->find($_POST['eventRoom']);
                $event->setYear(2014)
                      ->setCourse($course)
                      ->setRoom($room)
                      ->setDateFrom($df)
                      ->setDateTo($dt);
                $this->scheduleEventDao->create($event);

                if ($_POST['eventRepeat']) {
                    $repeats = (int) $_POST['eventRepeatWeeks'];
                    for ($i = 1; $i < $repeats; $i++) {
                        unset($repeatedEvent);
                        $repeatedEvent = clone $event;
                        $repeatedEvent->setId(null);
                        $event->setDateFrom($df->modify("+ 1 week"));
                        $event->setDateTo($dt->modify("+1 week"));
                        $this->scheduleEventDao->create($event);
                    }
                    Notification::information("Udalosť zopakovaná $i krát");
                }

                Notification::success('Záznam pridaný');
                $this->redirect($this->actionUrl('view'));
            }
        }

        $view->form = $form;

        return new Response(array(
                'Title' => 'Pridať udalosť',
                'ContentMain' => $view
            )
        );
    }

    protected function editEvent() {

        $view = new View('display/schedule/eventForm.php');

//        try {

        $event = $this->scheduleEventDao->find($_GET['id']);
        $form = EventForm::factory($event, $this->courseDao, $this->roomDao);
        $form->getField('action', true)->setValue('schedule/editEvent');

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $df = new DateTime('@' . strtotime($_POST['eventDateFrom']));
                $dt = new DateTime('@' . strtotime($_POST['eventDateTo']));
                $course = $this->roomDao->find($_POST['eventCourse']);
                $room = $this->roomDao->find($_POST['eventRoom']);
                $event->setYear($_POST['eventYear'])
                      ->setCourse($course)
                      ->setRoom($room)
                      ->setDateFrom($df)
                      ->setDateTo($dt);
                $this->scheduleEventDao->update($event);
                Notification::success('Záznam upravený');
                $this->redirect($this->actionUrl('view'));
            }
        }

        $view->form = $form;

        $view->imgSrc = '/tuke_' . strtolower($event->getRoom()->getBuilding()->getName()) . '.jpg';

        return new Response(array(
                'Title' => 'Upraviť udalosť',
                'ContentMain' => $view
            )
        );
//        } catch (RecordNotFoundException $e) {
//            Notification::warning('Udalosť nenájdená');
//            $this->redirect($this->actionUrl('view', array('interval', 'week')));
//        }
    }
}