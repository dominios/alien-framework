<?php

class UsersController extends AlienController {        
    
    protected function init_action() {

        $parentResponse = parent::init_action();
        if($parentResponse instanceof ActionResponse){
            $data = $parentResponse->getData();
        }

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'LeftTitle' => 'Používatelia',
            'ContentLeft' => $this->leftMenuItems(),
            'MainMenu' => $data['MainMenu']
        ), __CLASS__.'::'.__FUNCTION__);
    }

    private function leftMenuItems(){
        $items = Array();
        $items[] = Array('permissions' => null, 'url' => AlienController::actionURL('users', 'viewList'), 'img' => 'user.png', 'text' => 'Zoznam používateľov');
        $items[] = Array('permissions' => null, 'url' => AlienController::actionURL('users', 'edit', array('id' => 0)), 'img' => 'add_user.png', 'text' => 'Pridať používateľa');
        return $items;
    }

    protected function viewList(){

        $view = new AlienView('display/users/viewList.php', $this);
        $DBH = Alien::getDatabaseHandler();

        foreach($DBH->query('SELECT * FROM '.Alien::getDBPrefix().'_users') as $row){
            $users[] = new User(null, $row);
        }
        $view->Users = $users;

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => 'Zoznam používateľov',
            'ContentMain' => $view->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

    protected function edit(){

        if(!preg_match('/^[0-9]*$/', $_GET['id'])){
            new Notification('Neplatný identifikátor používateľa.', Notification::ERROR);
            return;
        }

        $View = new AlienView('display/users/edit.php', $this);
        $View->Id = (int)$_GET['id'];
        $View->User = new User((int)$_GET['id']);
        $View->ReturnAction = AlienController::actionURL('users', 'viewList');

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => (int)$_GET['id'] ? $View->User->getLogin() : 'Nový používateľ',
            'ContentMain' => $View->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

}
