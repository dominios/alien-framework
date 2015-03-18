<?php

namespace Alien\Forms\Schedule;

use Alien\Controllers\BaseController;
use Alien\Forms\Fieldset;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Input\Option;
use Alien\Models\School\Course;
use Alien\Models\School\CourseDao;
use Alien\Models\School\Room;
use Alien\Models\School\RoomDao;
use Alien\Models\School\ScheduleEvent;

class EventForm extends Form {

    /**
     * @var ScheduleEvent
     */
    protected $event;

    /**
     * @var CourseDao
     */
    protected $courseDao;

    /**
     * @var RoomDao
     */
    protected $roomDao;

    public function __construct() {
        parent::__construct('post', '', 'eventForm');
    }

    /**
     * @param ScheduleEvent $event
     * @return EventForm
     */
    public static function factory(ScheduleEvent $event, CourseDao $courseDao, RoomDao $roomDao) {
        parent::factory();

        $form = new EventForm();
        $form->event = $event;
        $form->courseDao = $courseDao;
        $form->roomDao = $roomDao;

        $form->setId('eventForm');
        $form->addClass('form-horizontal');

        Input::hidden('action', 'schedule/view/interval/week')->addToForm($form);
        Input::hidden('id', $event->getId())->addToForm($form);

        $generalFieldset = new Fieldset('general');

        if ($event->getId()) {
            Input::text('eventTeacher', 0, $event->getCourse()->getTeacher()->getFirstname() . ' ' . $event->getCourse()->getTeacher()->getSurname())
                 ->setLabel('Vyučujúci')
                 ->setDisabled(true)
                 ->addToFieldset($generalFieldset);
        }

        $room = Input::select('eventRoom')
                     ->setLabel('Miestnosť')
                     ->addToFieldset($generalFieldset);
        foreach ($form->roomDao->getList() as $i) {
            $name = $i->getBuilding()->getName() . ', ' . $i->getBuilding()->getStreet() . ', ' . $i->getFloor() . '. posch., ' . $i->getNumber();
            $opt = new Option($name, Option::TYPE_SELECT, $i->getId());
            if ($event->getRoom() instanceof Room) {
                if ($event->getRoom()->getId() == $i->getId()) {
                    $opt->setSelected(true);
                }
            }
            $room->addOption($opt);
        }

        $course = Input::select('eventCourse')
                       ->setLabel('Kurz')
                       ->addToFieldset($generalFieldset);
        if ($event->getId()) {
            $course->setDisabled(true);
        }

        $course->addOption(new Option(' --- Vybrať kurz', Option::TYPE_SELECT, ""));

        foreach ($form->courseDao->getList() as $i) {
            $name = $i->getName();
            $opt = new Option($name, Option::TYPE_SELECT, $i->getId());
            if ($event->getCourse() instanceof Course) {
                if ($event->getCourse()->getId() == $i->getId()) {
                    $opt->setSelected(true);
                }
            }
            $course->addOption($opt);
        }

        $dateFromDefault = $event->getCourse() instanceof Course ? $event->getCourse()->getDateStart() : $event->getDateFrom();
        Input::dateTimeLocal('eventDateFrom', null, $dateFromDefault)
             ->setLabel('Začiatok')
             ->addToFieldset($generalFieldset);

        $dateEndDefault = $event->getCourse() instanceof Course ? $event->getCourse()->getDateStart() : $event->getDateFrom();
        Input::dateTimeLocal('eventDateTo', null, $dateEndDefault)
             ->setLabel('Koniec')
             ->addToFieldset($generalFieldset);

        if (!$event->getId()) {

            Input::checkbox('eventRepeat', 1, false)
                 ->setLabel('Opakovať')
                 ->addToFieldset($generalFieldset);

            Input::text('eventRepeatWeeks', 0, '')
                 ->setLabel('Počet týždňov')
                 ->addToFieldset($generalFieldset);
        }

        $submitFieldset = new Fieldset("submit");
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        Input::button(BaseController::staticActionURL('schedule', 'view', array('interval' => 'week')), 'Zrušiť')
             ->addCssClass('btn-danger')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button("javascript: $('#eventForm').submit();", 'Uložiť')
             ->addCssClass('btn-success')
             ->setName('buttonSave')
             ->addToFieldset($submitFieldset);

        $form->addFieldset($generalFieldset);
        $form->addFieldset($submitFieldset);

        return $form;

    }
}