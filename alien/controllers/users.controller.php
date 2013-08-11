<?php

class UsersController extends AlienController {        
    
    protected function init_action() {

        $parentResponse = parent::init_action();
        if($parentResponse instanceof ActionResponse){
            $data = $parentResponse->getData();
        }

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'ContentLeft' => $this->left(),
            'MainMenu' => $data['MainMenu']
        ), __CLASS__.'::'.__FUNCTION__);
    }

    private function left(){
        $ret = '';
        $ret .= '<h3>Používatelia</h3>';
        $ret .= '<a href="?users=viewList"><img src="'.Alien::$SystemImgUrl.'/user.png">Zoznam používateľov</a>';
        $ret .= '<a href="?users=edit&id=0"><img src="'.Alien::$SystemImgUrl.'/add_user.png">Pridať používateľa</a>';
//        $ret .= '<a href="?users=groupList"><img src="'.Alien::$SystemImgUrl.'/group.png">Zoznam skupín</a>';
//        $ret .= '<a href="?users=editGroup&id=0"><img src="'.Alien::$SystemImgUrl.'/add_group.png">Pridať skupinu</a>';
//        $ret .= '<a href="?users=permissionList"><img src="'.Alien::$SystemImgUrl.'/locked.png">Zoznam oprávnení</a>';
        return $ret;
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
        $View->ReturnAction = '?users=viewList';

        return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => (int)$_GET['id'] ? $View->User->getLogin() : 'Nový používateľ',
            'ContentMain' => $View->getContent()
        ), __CLASS__.'::'.__FUNCTION__);
    }

}
