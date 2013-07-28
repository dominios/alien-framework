<?php
class AlienView {
    
    private $script;
    
    public function __construct($script) {
        $this->script = $script;
    }
    
    public function getContent(){
        $content = '';
        if(file_exists($this->script)){
            ob_start();
            include $this->script;
            $content .= ob_get_contents();
            ob_end_clean();
        } else {
            AlienConsole::getInstance()->putMessage('Cannot open view <i>'.$this->script.'</i>', AlienConsole::CONSOLE_WARNING);
        }
        return $content;
    }
}
?>
