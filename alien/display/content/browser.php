<?php

echo ('<div id="viewContent" class="' . ($this->DisplayLayout === 'ROW' ? 'tableLayout' : 'gridLayout') . '">');

if ($this->Folder->getId() != 0 && $this->DisplayLayout === 'ROW') {
    echo ('<div class="item selectable" onClick="javascript: window.location=\'' . $this->Folder->getParent(true)->actionGoTo() . '\'">');
    echo ('<img src="' . \Alien\Application::$SystemImgUrl . $this->Folder->getIcon() . '">[..]');
    //echo $this->renderControlPanel();
    //echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;Typ: Adresár');
    echo ('</div>');
}

if ($this->Folder->getId() != 0 && $this->DisplayLayout === 'GRID') {
    // TODO: grid layout parent folder
}

foreach ($this->Folders as $folder) {

    if ($this->DisplayLayout === 'ROW') {
        echo ('<div class="item selectable" onClick="javascript: window.location=\'' . $folder->actionGoTo() . '\'">');
        echo ('<img src="' . \Alien\Application::$SystemImgUrl . $folder->getIcon() . '">' . $folder->getName() . '');
        //echo $this->renderControlPanel();
        //echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;Typ: Adresár');
        echo ('</div>');
    }
    if ($this->DisplayLayout === 'GRID') { /*
      echo ('<div class="item selectable">');
      echo ('<div class="itemIcon"><img src="'.Alien::$SystemImgUrl.$folder->getIcon().'"></div>');
      echo ('<div class="itemText">'.$options['name'].'</div>');
      echo ('</div>'); */
    }
}


foreach ($this->Items as $item) {

    if (!$item->isBrowseable()) {
        continue;
    }

    if ($this->DisplayLayout === 'ROW') {
        echo ('<div class="item selectable" onClick="javascript: window.location=\'' . $item->actionEdit() . '\'">');
        echo ('<img src="' . \Alien\Application::$SystemImgUrl . $item->getIcon() . '">' . $item->getName() . '');
        //echo $this->renderControlPanel();
        //echo ('&nbsp;&nbsp;ID: '.$this->id.'&nbsp;|&nbsp;Typ: Adresár');
        echo ('</div>');
    }
    if ($this->DisplayLayout === 'GRID') {

    }
}
echo ('</div>');