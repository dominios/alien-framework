<?php

namespace Alien\Controllers;

use Alien\Models\Authorization\Authorization;
use Alien\Models\School\CourseDao;
use Alien\Response;
use Alien\Table\DataTable;
use Alien\Table\Table;

class CourseController extends BaseController {

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var CourseDao
     */
    private $courseDao;

    protected function initialize() {

        $this->authorization = $this->getServiceManager()->getService('Authorization');
        $this->courseDao = $this->getServiceManager()->getDao('CourseDao');

        parent::initialize();

//        $parentResponse = parent::initialize();
//        if ($parentResponse instanceof Response) {
//            $data = $parentResponse->getData();
//        }

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
                        return "<a href=\"/alien/course/edit/$row[id]\">UPRAVIŤ</a>";
                    }
            ));
        };

        $this->view->table = $table;

        return new Response(array(
                'Title' => 'Zoznam kurzov',
                'ContentMain' => $this->view
            )
        );
    }

    protected function editAction() {

        $course = $this->courseDao->find($this->getParam('id'));

        $this->view->course = $course;

        return new Response(array(
            'Title' => $course->getName() . ' | ' . 'Administrácia  kurzu',
            'ContentMain' => $this->view
        ));
    }
}