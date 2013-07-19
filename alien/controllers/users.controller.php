<?php

class UsersController extends AlienController {        
    
    protected function init_action() {
        parent::init_action();
        $this->meta_title = 'Používatelia systému';        
        $this->content_left = $this->left();
    }
    
    protected function viewList(){
        
        $view = new AlienView('display/users/viewList.php');
        $DBH = Alien::getDatabaseHandler();        
        
        foreach($DBH->query('SELECT * FROM '.Alien::getDBPrefix().'_users') as $row){
            $users[] = new User(null, $row);
        }
        $view->Users = $users;
        return $view->getContent();
    }
    
    private function left(){
        $ret = '';
        $ret .= '<h3>Používatelia</h3>';
        $ret .= '<a href="?users=viewList"><img src="'.Alien::$SystemImgUrl.'/user.png">Zoznam používateľov</a>';
        $ret .= '<a href="?users=editUser&id=0"><img src="'.Alien::$SystemImgUrl.'/add_user.png">Pridať používateľa</a>';
//        $ret .= '<a href="?users=groupList"><img src="'.Alien::$SystemImgUrl.'/group.png">Zoznam skupín</a>';
//        $ret .= '<a href="?users=editGroup&id=0"><img src="'.Alien::$SystemImgUrl.'/add_group.png">Pridať skupinu</a>';
//        $ret .= '<a href="?users=permissionList"><img src="'.Alien::$SystemImgUrl.'/locked.png">Zoznam oprávnení</a>';
        return $ret;
    }
}
?>
