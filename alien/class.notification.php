<?php

class Notification {
    
    private $message;
    private $type;
    private $seen = false;

    const INFO = 'note';
    const SUCCESS = 'success';
    const WARNING = 'warning';
    const ERROR = 'error';
    
    private static $messageList = array();
    
    /**
     * New notification constructor
     * @param string $msg message to display
     * @param string $type type of message, one of the following: <b>note</b>, <b>success</b>, <b>warning</b> or <b>error</b>
     */
    public function __construct($msg,$type){
        $this->message=$msg;
        $this->type=$type;
        self::addNoteToList($this);
        $_SESSION['notifications']=serialize(self::getList());
//        $_SESSION['notifications'][]=$this;
    }

    public static function renderNotifications(){
        $ret = '';
        if(isset($_SESSION['notifications'])){
            $notifications = unserialize($_SESSION['notifications']);
            foreach($notifications as $note ){
                switch($note->type){
                    case self::INFO:
                        $ret .= ('<div class="notification information"><img src="images/icons/information.png">&nbsp;'.$note->message.'</div>');
                        break;
                    case self::SUCCESS:
                        $ret .= ('<div class="notification success"><img src="'.Alien::$SystemImgUrl.'/tick.png">&nbsp;'.$note->message.'</div>');
                        break;
                    case self::WARNING:
                        $ret .= ('<div class="notification warning"><img src="'.Alien::$SystemImgUrl.'/warning.png">&nbsp;'.$note->message.'</div>');
                        break;
                    case self::ERROR:
                        $ret .= ('<div class="notification error"><img src="'.Alien::$SystemImgUrl.'/cross.png">&nbsp;'.$note->message.'</div>');
                        break;
                }
            }
            //unset($_SESSION['notifications']);
        }
        echo $ret;
    }


    public static function addNoteToList(Notification $note){
        self::$messageList[]=$note;
    }
    
    public static function getList(){
        return self::$messageList;
    }
    
    
}
?>
