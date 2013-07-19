<?php

class AlienController {   
    
    protected $defaultAction;
    private $actions;
    private $view;

    protected $meta_title; 
    protected $content_mainmenu;
    protected $content_left;
    protected $content_main;
   
   public final function __construct() {
       Alien::getInstance()->getConsole()->putMessage('Using <i>'.get_called_class().'</i>.');
       $this->defaultAction = 'NOP';       
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
       Alien::getInstance()->getConsole()->putMessage('Called <i>AlienController.init_action()</i>.');   
       $menu = '';
       $menuitems = Alien::getInstance()->getMainmenuItems();
       foreach($menuitems as $item){
           // perm test !
           $menu .= '<a href="'.$item['url'].'" '.(isset($item['onclick']) ? 'onclick="'.$item['onclick'].'"' : '').'><img src="'.Alien::$SystemImgUrl.$item['img'].'">'.$item['label'].'</a>';
       }
       
       $this->meta_title = 'HOME';
       $this->content_left = '';
       $this->content_main = '';
       $this->content_mainmenu = $menu;

   }

   private final function doActions(){
       $out = '';
       if(method_exists(get_called_class(), 'init_action')){
           $this->init_action();
           Alien::getInstance()->getConsole()->putMessage('Called <i>'.get_called_class().'.init_action()</i>.');
       }
       foreach($this->actions as $action){    
            Alien::getInstance()->getConsole()->putMessage('Calling action: <i>'.get_called_class().'.'.$action.'</i>()');
            if(!method_exists($this, $action)){           
                 Alien::getInstance()->getConsole()->putMessage('Action doesn\'t exist!', AlienConsole::CONSOLE_ERROR);
            }
            if(!method_exists($this, $action) && $action!=$this->defaultAction){
                 $action = $this->defaultAction;
                 Alien::getInstance()->getConsole()->putMessage('Calling action <i>'.get_called_class().'.'.$action.'</i>() instead.');
            }
            $ret = $this->$action();
            $out .= $ret!==false ? $ret : '';
            Alien::getInstance()->getConsole()->putMessage('Action <i>'.$action.'</i>() done.');
       }
       
       $this->content_main = $out;
   }
   
   // TODO: konzola zatial natvrdo
   public final function getContent(){       
       
        $this->doActions();
        
        $this->view = new AlienView('display/index.php');        
        $this->view->Title = $this->meta_title;
        $this->view->MainMenu = $this->content_mainmenu;
        $this->view->LeftBox = $this->content_left;
        $this->view->MainContent = $this->content_main;
        $content = $this->view->getContent();

        if(Alien::getParameter('debugMode') && false || 1){
            $console = new AlienView('display/console.php');
            $console->Messages = Alien::getInstance()->getConsole()->getMessageList();
            $content .= $console->getContent();
        }

        return $content;
   }
      
   protected function NOP(){
       return '';
   }
   
   
}

?>
