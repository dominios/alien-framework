<?php

namespace Alien\Forms\Course;

use Alien\Forms\Fieldset;
use Alien\Forms\Form;
use Alien\Forms\Input;
use Alien\Forms\Input\Option;
use Alien\Models\Authorization\UserDao;
use Alien\Models\School\Course;
use Alien\Router;
use DateTime;

class CourseForm extends Form {

    /**
     * @var Course
     */
    private $course;

    /**
     * @var UserDao
     */
    private $userDao;


    public function __construct() {
        parent::__construct('post', '', 'courseForm');
    }

    public static function factory(Course $course, UserDao $userDao) {
        parent::factory();

        $form = new CourseForm();
        $form->userDao = $userDao;
        $form->course = $course;

        $form->addClass('form-horizontal');
        $form->setId('courseForm');

        Input::hidden('action', Router::getRouteUrl('course/edit/' . $course->getId()))->addToForm($form);
        Input::hidden('id', $course->getId())->addToForm($form);

        $generalFieldset = new Fieldset('general');

        Input::text('courseName', '', $course->getName())
             ->setLabel('Názov kurzu')
             ->addToFieldset($generalFieldset);

        Input::text('courseCapacity', '', $course->getCapacity())
             ->setLabel('Kapacita')
             ->addToFieldset($generalFieldset);


        $teacher = Input::select('courseTeacher')
                        ->setLabel('Učiteľ')
                        ->addToFieldset($generalFieldset);

        foreach ($form->userDao->getList() as $i) {
            $opt = new Option($i->getLogin(), Option::TYPE_SELECT, $i->getId());
            if ($course->getTeacher() instanceof User) {
                if ($course->getTeacher()->getId() == $i->getId()) {
                    $opt->setSelected(true);
                }
            }
            $teacher->addOption($opt);
        }

        Input::dateTimeLocal('courseDateStart', new DateTime(), $course->getDateStart())
             ->setLabel('Dátum začiatku')
             ->addToFieldset($generalFieldset);

        Input::dateTimeLocal('courseDateEnd', new DateTime(), $course->getDateEnd())
             ->setLabel('Dátum konca')
             ->addToFieldset($generalFieldset);

        Input::color('courseColor', '', '#' . $course->getColor())
             ->setLabel('Farba')
             ->addToFieldset($generalFieldset);

        $submitFieldset = new Fieldset('submit');
        $submitFieldset->setViewSrc('display/common/submitFieldset.php');

        Input::button(Router::getRouteUrl('course'), 'Zrušiť')
             ->addCssClass('btn-danger')
             ->setName('buttonCancel')
             ->addToFieldset($submitFieldset);

        Input::button("javascript: $('#courseForm').submit();", 'Uložiť')
             ->addCssClass('btn-success')
             ->setName('buttonSave')
             ->addToFieldset($submitFieldset);

        $form->addFieldset($generalFieldset);
        $form->addFieldset($submitFieldset);

        return $form;

    }


} 