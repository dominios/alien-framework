<?php

class AlienController {   

    protected $defaultAction = 'NOP';
    protected $actions;

    private $layout;

    public final function __construct() {
       Alien::getInstance()->getConsole()->putMessage('Using <i>'.get_called_class().'</i>');
       $actions = Array();
       if(@isset($_POST['action'])){
           $actions[] = $_POST['action'];
       }
       if(sizeof($_GET)){
           $actions[] = $_GET[key($_GET)];
       }
//       if(@isset($_GET['action'])){
//           $actions[] = $_GET['action'];
//       }
       if(!sizeof($actions)){
           $actions[] = $this->defaultAction;
       }
       $this->actions = $actions;       
   }

   protected function init_action(){

        $auth = Authorization::getInstance();
        if(!$auth->getInstance()->isLoggedIn() && !in_array('login', $this->actions)){
            unset($this->actions);
            $this->setLayout(new LoginPageLayout());
            return;
        }

       $this->setLayout(new AlienAdminLayout());

        Alien::getInstance()->getConsole()->putMessage('Called <i>AlienController::init_action()</i>.');
        $menu = '';
        $menuitems = Alien::getInstance()->getMainmenuItems();
        foreach($menuitems as $item){
           // perm test dorobit !
           $menu .= '<a href="'.$item['url'].'" '.(isset($item['onclick']) ? 'onclick="'.$item['onclick'].'"' : '').'><img src="'.Alien::$SystemImgUrl.$item['img'].'">'.$item['label'].'</a>';
        }

       return new ActionResponse(ActionResponse::RESPONSE_OK, Array(
            'Title' => 'HOME',
            'ContentLeft' => '',
            'ContentMain' => '',
            'MainMenu' => $menu
       ), __CLASS__.'::'.__FUNCTION__);

   }

   private final function doActions(){

       $responses = Array();

       if(method_exists(get_called_class(), 'init_action')){
           $response = $this->init_action();
           if($response instanceof ActionResponse){
               array_push($responses, $response);
           }
           Alien::getInstance()->getConsole()->putMessage('Called <i>'.get_called_class().'::init_action()</i>.');
       }
       foreach($this->actions as $action){    
            Alien::getInstance()->getConsole()->putMessage('Calling action: <i>'.get_called_class().'::'.$action.'</i>()');
            if(!method_exists($this, $action)){           
                 Alien::getInstance()->getConsole()->putMessage('Action <i>'.$action.'</i> doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
            }
            if(!method_exists($this, $action) && $action!=$this->defaultAction){
                 $action = $this->defaultAction;
                 Alien::getInstance()->getConsole()->putMessage('Calling action <i>'.get_called_class().'::'.$action.'</i>() instead.');
            }
            $response = $this->$action();
            if($response instanceof ActionResponse){
                array_push($responses, $response);
            }
            Alien::getInstance()->getConsole()->putMessage('Action <i>'.$action.'</i>() done.');
       }

       return $responses;

   }

    public function getLayout(){
        return $this->layout;
    }

    public function setLayout(ALienLayout $layout){
        $this->layout = $layout;
    }

    protected function redirect($location, $statusCode = 301){
        ob_clean();
        $this->getLayout()->saveSessionNotifications();
        header('Location: '.$location, false, $statusCode);
        ob_end_flush();
        exit;
    }

   // TODO: konzola zatial natvrdo
    public final function getContent(){

        $responses = $this->doActions();

        foreach($responses as $response){
            $this->getLayout()->handleResponse($response);
//            AlienConsole::getInstance()->putMessage('Response from <i>'.$response->getAction().'</i> handled.');
        }

        $content = $this->layout->renderToString();

        return $content;
   }
      
    protected function NOP(){
       return;
   }

    private function login(){
        if(isset($_POST['loginFormSubmit'])){
            if(!Authorization::getInstance()->isLoggedIn()){
                Authorization::getInstance()->login($_POST['login'], $_POST['pass']);
            }
        }
        $this->redirect('index.php');
    }

    private function logout(){
        Authorization::getInstance()->logout();
        $this->redirect('index.php');
    }
}
