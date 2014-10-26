<?php

namespace Alien\Controllers;

use Alien\Response;

class CourseController extends BaseController {

    protected function initialize() {

        $this->defaultAction = 'view';

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

    protected function view() {
//        $view = new View('display/users/viewList.php', $this);
//        $view->users = User::getList(true);
//        $view->editActionPattern = BaseController::actionURL('users', 'edit', array('id' => '%ID%'));
//        $view->sendMessagePattern = BaseController::actionURL('dashboard', 'composeMessage', array('id' => '%ID%'));
        return new Response(array(
                'Title' => 'Zoznam používateľov',
                'ContentMain' => "test"
            )
        );
    }
}