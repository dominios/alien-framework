<?php

var_dump($this->Folders); return;

echo ('<div id="viewContent" class="'.($this->DisplayLayout === 'ROW' ? 'tableLayout' : 'gridLayout').'">');


foreach($this->Folders as $folder){
    
    if($this->DisplayLayout === 'ROW'){

        echo ('<div class="item selectable" onClick="javascript: window.location=\'?page=content&amp;action=browser&amp;folder='.$folder->getId().'\'">');
        echo ('<img src="'.Alien::$SystemImgUrl.$folder->getIcon().'"> <b>'.$folder->getName().'</b>');
            //echo $this->renderControlPanel();
        echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;Typ: Adresár');
        echo ('</div>');
    
    } else {
        
        echo ('<div class="item selectable">');
            echo ('<div class="itemIcon"><img src="'.Alien::$SystemImgUrl.$folder->getIcon().'"></div>');
            echo ('<div class="itemText">'.$options['name'].'</div>');
        echo ('</div>');
        
    }
}


echo ('</div>');
?>
