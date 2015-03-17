<?php

namespace Alien\Controllers;

use Alien\Forms\Course\CourseForm;
use Alien\Forms\Input;
use Alien\Models\Authorization\Authorization;
use Alien\Models\School\Course;
use Alien\Models\School\CourseDao;
use Alien\Models\School\TeacherDao;
use Alien\Notification;
use Alien\Response;
use Alien\Router;
use Alien\Table\DataTable;
use Alien\Table\Table;
use Alien\View;
use DateTime;

class CourseController extends BaseController {

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var CourseDao
     */
    private $courseDao;

    /**
     * @var TeacherDao
     */
    private $teacherDao;

    protected function initialize() {

        $this->authorization = $this->getServiceManager()->getService('Authorization');
        $this->courseDao = $this->getServiceManager()->getDao('CourseDao');
        $this->teacherDao = $this->getServiceManager()->getDao('TeacherDao');

        parent::initialize();

        return new Response(array(
                'LeftTitle' => 'Kurzy',
                'ContentLeft' => array(),
                'MainMenu' => ""
            )
        );
    }

    protected function listAction() {

        $data = $this->courseDao->getTableData($this->courseDao->getList());

        $table = new DataTable($data);
        $table->setName('Zoznam kurzov');
        if ($this->authorization->getCurrentUser()->hasPermission('COURSE_ADMIN')) {
            $table->addHeaderColumn(array('edit' => ''));
            $table->addRowColumn(array(
                'edit' => function ($row) {
                        $ret = "";
                        $urlEdit = "/alien/course/edit/$row[id]";
                        $urlRemove = "/alien/course/remove/$row[id]";
                        $ret .= "<a href=\"$urlEdit\"><i class=\"fa fa-pencil\"></i></a>";
                        $ret .= " <a href=\"$urlRemove\"><i class=\"fa fa-trash-o text-danger\"></i></a>";
                        return $ret;
                    }
            ));
        };

        $this->view->table = $table;
        $this->view->addButton = Input::button(Router::getRouteUrl('course/new'), 'Nový kurz')->addCssClass('btn-primary')->setIcon('fa fa-plus');

        return new Response(array(
                'Title' => 'Zoznam kurzov',
                'ContentMain' => $this->view
            )
        );
    }

    protected function editAction() {

        $course = $this->courseDao->find($this->getParam('id'));

        $form = CourseForm::factory($course, $this->teacherDao);

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                if ($course instanceof Course) {
                    $course->setName($_POST['courseName'])
                           ->setCapacity($_POST['courseCapacity'])
                           ->setColor(str_replace('#', '', $_POST['courseColor']))
                           ->setTeacher($this->teacherDao->find($_POST['courseTeacher']))
                           ->setDateStart(new DateTime('@' . strtotime($_POST['courseDateStart'])))
                           ->setDateEnd(new DateTime('@' . strtotime($_POST['courseDateEnd'])));
                    $this->courseDao->update($course);
                    Notification::success('Kurz upravený');
                    $this->redirect(Router::getRouteUrl('course'));
                }
            }
        }

        $this->view->course = $course;
        $this->view->form = $form;

        return new Response(array(
            'Title' => $course->getName() . ' | ' . 'Administrácia  kurzu',
            'ContentMain' => $this->view
        ));
    }

    protected function newAction() {

        $view = new View('display/course/editAction.php');

        $course = new Course();
        $form = CourseForm::factory($course, $this->teacherDao);

        if ($form->isPostSubmit()) {
            if ($form->validate()) {
                $course->setName($_POST['courseName'])
                       ->setCapacity($_POST['courseCapacity'])
                       ->setColor(str_replace('#', '', $_POST['courseColor']))
                       ->setTeacher($this->teacherDao->find($_POST['courseTeacher']))
                       ->setDateCreated(new DateTime('now'))
                       ->setDateStart(new DateTime('@' . strtotime($_POST['courseDateStart'])))
                       ->setDateEnd(new DateTime('@' . strtotime($_POST['courseDateEnd'])));
                $this->courseDao->create($course);
                $this->courseDao->update($course);
                Notification::success('Kurz vytvorený');
                $this->redirect(Router::getRouteUrl('course'));
            }
        }

        $view->form = $form;

        return new Response(array(
                'Title' => 'Pridať kurz',
                'ContentMain' => $view
            )
        );
    }

    protected function removeAction() {
        $course = $this->courseDao->find($this->getParam('id'));
        if ($course instanceof Course) {
            $this->courseDao->delete($course);
        }
        $this->redirect(Router::getRouteUrl('course'));
    }
}