<?php

class Notification {
    
    private $message;
    private $type;
    private $seen = false;

    const NOT_INFO = 'note';
    const NOT_SUCCESS = 'success';
    const NOT_WARNING = 'warning';
    const NOT_ERROR = 'error';
    
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
        $_SESSION['notifications']=self::getList();
//        $_SESSION['notifications'][]=$this;
    }

    public static function renderNotifications(){
        $ret = ('<div style="display: none;" id="notificationsTemp">');
            if(@isset($_SESSION['notifications'])){
                foreach($_SESSION['notifications'] as $note ){
                    switch($note->type){
                        case self::NOT_INFO:
                            echo ('<div class="notification information"><img src="images/icons/information.png">&nbsp;'.$note->message.'</div>');
                            break;
                        case self::NOT_SUCCESS:
                            echo ('<div class="notification success"><img src="'.Alien::$SystemImgUrl.'/tick.png">&nbsp;'.$note->message.'</div>');
                            break;
                        case self::NOT_WARNING:
                            echo ('<div class="notification warning"><img src="'.Alien::$SystemImgUrl.'/warning.png">&nbsp;'.$note->message.'</div>');
                            break;
                        case self::NOT_ERROR:
                            echo ('<div class="notification error"><img src="'.Alien::$SystemImgUrl.'/cross.png">&nbsp;'.$note->message.'</div>');
                            break;
                    }
                }
                unset($_SESSION['notifications']);
            }
        return $ret;
    }


    public static function addNoteToList(Notification $note){
        self::$messageList[]=$note;
    }
    
    public static function getList(){
        return self::$messageList;
    }
    
    
}
?>
